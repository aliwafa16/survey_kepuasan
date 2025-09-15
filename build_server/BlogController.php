<?php

namespace App\Http\Controllers;
use App\Models\Blogs;
use App\Models\Subscribes;
use App\Models\Categories;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Request as ajax;
use Validator;
use Auth;
use Illuminate\Support\Facades\DB;
use Mail;
class BlogController extends Controller
{
   
    public function index(Request $request)
    {        
         if($request->ajax()){
            $query = Blogs::select('blogs.*','categories.name','users.name as author')
                          ->join('categories','categories.category_id','=','blogs.category_id')
                          ->join('users','users.id','=','blogs.created_by')
                        //   ->orderBy('blog_id', 'asc')
                          ->get();
            return Datatables::of($query)
            ->filterColumn('author', function ($query, $keyword) {
                $sql = "users.name like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            })->make(true);
            }
        $jumlah = Blogs::select('blogs.blog_id')->count();

        return view('blogs.index')->with(compact('jumlah'));
    }

        public function index_trash(Request $request)
    {        
         if($request->ajax()){
            $query = DB::table('blogs')
                          ->select('blogs.*','categories.name','users.name as author')
                          ->join('categories','categories.category_id','=','blogs.category_id')
                          ->join('users','users.id','=','blogs.created_by')
                          ->where('blogs.deleted_at','!=',null)
                          ->get();
            return Datatables::of($query)
            ->filterColumn('author', function ($query, $keyword) {
                $sql = "users.name like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            })->make(true);
            }
        

        return view('blogs.index');
    }


    
    public function create()
    {
        $categories =  Categories::select('*')->groupBy('slug')->orderBy('parent')->get();
        return view('blogs.create',compact('categories'));
    }

    
    public function store(Request $request)
    {
        $input = $request->except(['_token']);
        $file = $request->image->getClientOriginalName();
        $unique = uniqid();
        if ($input['description'] == '<br>' || $input['description'] == "<p><br></p>") {
                    $request['description']="";
            }

        if($input['jenis_file'] ==  'image'){
            $rule_image = 'required|image|mimes:jpg,icon,png,jpeg,gif';
        }else{
            $rule_image = 'required';
        }

        $validator = Validator::make($request->all(), [
            'title'               => 'required|max:250',
            'sort_order'          => 'required|numeric',
            'description'         => 'required',
            'category_id'         => 'required|max:250',
            'active'              => 'required',
            'description'         => 'required',
            'published_at'        => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput($request->all());
        }
        if($input['jenis_file'] ==  'image'){
            if($request->hasFile('image')){
               $image=$request->file('image');
                $getimageName = $unique.'.'.$request->image->getClientOriginalExtension();
                $request->image->move('uploads/images', $getimageName);
                $input['image']=$getimageName;
            }
                if($request->hasFile('ot')){
               $ot=$request->file('ot');
                foreach($ot as $key => $name){
                $getimageName = $key.$unique.'.'.$request->ot[$key]->getClientOriginalExtension();
                $request->ot[$key]->move('uploads/images', $getimageName);
                        $ot[$key]=$getimageName;
                        
                }
            
            }

        }
        if( isset($input['ot']) && count($input['ot']) > 0){  
        $input['other_images']      =  implode("|",$ot);
        }else{
        $input['other_images']      =  '';    
        }
        $input['category_id']       =   $input['category_id'];
        $input['created_by']        =   Auth::user()->id;
        $input['reporter']          =   $input['reporter'].$input['namaReporter'];
        $input['meta_description']  =   strip_tags($input['description']);
        $input['meta_title']        =   $input['title'];
        $input['meta_keyword']      =   $input['title'];
      



        $create = Blogs::create($input);

        $data = [
            'title' => $input['title'],
            'image' => $input['image'],
            'desc' => $input['description'],
        ];

        if(empty($create)){
            \Session::flash('error','Created Data failed !');
            return redirect()->back();
        }

       /* Mail::send('mail', $data ,function($message){
                $subs = Subscribes::all();
                foreach ($subs as $subs){
                $message->to($subs->email,'To Subscribers')->subject('NEW ARTICLE');
                $message->from('redaksi@esq165.co.id','ESQ NEWS');
                }
            });
    */
        return redirect('blogs')->with('message','Data created sucessfully!'); 
  
  
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Blogs::where('slug',$id)->first();
        $ex = explode(',',$data->category_id);
        $back = 'blogs';
        $cate = array();
        foreach ($ex as $value) {
            $get=Categories::where('category_id',$value)->first();
            array_push($cate,$get->name);
        }
        if(empty($data)){
            \Session::flash('message','Data not Found !');
            return redirect()->back();
        }

        return view('blogs.show',compact('data'))
                                ->with(compact('back'))
                                ->with(compact('cate'));
    }

    
    public function edit($id)
    {   
        $categories = Categories::get();
        $data       = Blogs::where('slug',$id)->first();
        $ex         = explode(',',$data->category_id);
        if(empty($data)){
            \Session::flash('message','Data not Found !');
            return redirect()->back();
        }

        return view('blogs.edit',compact('data'))
                                ->with(compact('categories'))
                                ->with(compact('ex'));
    }

    
    public function update(Request $request, $id)
    {
          $data = Blogs::where('blog_id',$id)->first();
          $ot_old = $request->ot_old;//explode('|',$data->other_images);
          $unique = uniqid();
         $input=$request->except(['_token','ot2','ot_new','ot_old','files']);
      //   return $request->all();
         
         
          $ot_new=$request->file('ot_new');
          $x=array();

          if(isset($ot_old) && count($ot_old) > 0)
            foreach ($ot_old as $key =>  $value) {
             $x[$key]=$value;           
            }else{
            $key = 0;
            }

          if(count($ot_new) != 0){
 
            foreach ($ot_old as $key => $value) {
                if(isset($request->ot_new[$key])){
                     if(file_exists('uploads/images/'.$value)){
                 //           unlink('uploads/images/'.$value);
                        }
                   // unlink('uploads/images/'.$value);
                    $getimageName = $key.$unique.'.'.$request->ot_new[$key]->getClientOriginalExtension();
                    $request->ot_new[$key]->move('uploads/images', $getimageName);
                    $x[$key] =  $getimageName;
                }else{
                    $x[$key] = $value;
                }   
            }
          }
          if(count($request->ot2) > 0)
            {
                $num =0;
                foreach ($request->ot2 as $k => $value) {
                    $getimageName = $k.$unique.'.'.$request->ot2[$k]->getClientOriginalExtension();
                    $request->ot2[$k]->move('uploads/images', $getimageName);
                    $num++;
                    $x[$key+$num] =  $getimageName;
                 }   
            }
      
        $input['other_images']      =  implode("|",$x);
        if(empty($data)){
            \Session::flash('message','Data not Found !');
            return redirect()->back();
        }
 


         if ($input['description'] == '<br>' || $input['description'] == "<p><br></p>") {
                           $request['description']="";
                                        }
        
                                        
        $validator = Validator::make($request->all(), [
            'title'               => 'required|max:250',
            'sort_order'          => 'required|numeric',
            'description'         => 'required',
            'category_id'         => 'required|max:250',
            'active'              => 'required',
            'description'         => 'required',
            'published_at'        => 'required',

        ]);
                if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput($request->all());
        }

        if($request->hasFile('image')){

        $getimageName = $unique.'.'.$request->image->getClientOriginalExtension();
        $request->image->move('uploads/images', $getimageName);
        $input['image']= $getimageName;
        }else{
            $input['image']= $data->image;
        }
        


        $input['category_id']       =  $input['category_id'];
        $input['created_by']        =   Auth::user()->id;
        $input['meta_description']  =   strip_tags($input['description']);
        $input['meta_title']        =   $input['title'];
        $input['meta_keyword']      =   $input['title'];

        Blogs::where('blog_id',$id)->update($input);
        return redirect('blogs')->with('message','Data updated sucessfully!');
    
    }

    
    public function destroy($id)
    {
        $data = Blogs::where('blog_id',$id)->first();

        if(empty($data)){
            \Session::flash('message','Data not Found !');
            return redirect()->back();
        }

      $delete = Blogs::where('blog_id',$id)->delete();
  }
  public function add_image_body(Request $request){
            if ($_FILES['file']['name']) {
                if (!$_FILES['file']['error']) {
                    $name = uniqid();
                    $ext = explode('.', $_FILES['file']['name']);
                    $filename = $name . '.' . end($ext);
                    $destination = './uploads/images/' . $filename; //change this directory
                    $location = $_FILES["file"]["tmp_name"];
                    move_uploaded_file($location, $destination);
                    return '/uploads/images/'. $filename;//change this URL
                }
                else
                {
                  echo  $message = 'Ooops!  Your upload triggered the following error:  '.$_FILES['file']['error'];
                }
            }
        }
    public function deleteFile(Request $request){
      $input= $request->all();
      $src = $input['src']; // $src = $_POST['src'];
     
       if(unlink('uploads/images/'.$src))
        {
         return 'File Delete Successfully';
        }
    }

    public function show_trash($id)
    {
        $data = DB::table('blogs')->where('slug',$id)->first();
        $ex = explode(',',$data->category_id);
        $back = 'trash';
        $cate = array();
        foreach ($ex as $value) {
            $get=Categories::where('category_id',$value)->first();
            array_push($cate,$get->name);
        }
        if(empty($data)){
            \Session::flash('message','Data not Found !');
            return redirect()->back();
        }

        return view('blogs.show',compact('data'))
                                ->with(compact('back'))
                                ->with(compact('cate'));
    }

    public function restore($id)
    {
        $data = DB::table('blogs')->where('blog_id',$id)->update(['deleted_at'=>null,'deleted_by'=>null]);
           return redirect()->back()->with('message','Data restore sucessfully!');
    }

}
