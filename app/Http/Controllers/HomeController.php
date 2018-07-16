<?php
namespace App\Http\Controllers\InfiniteScrolling;
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Mail\Mailable;
use App\User;
use DB;
use AUTH;


class HomeController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }
    public function index()
    {
        return view('home');
    }
    public function payment()
    {
        return view('frontend.payment');
    }

	public function homepage(Request $request)
	{
		$perpage = 6;
		$page_type 	= 'homepage';
		$victory 	= DB::table('petition_master')->where('is_victory' , '=' , '1')->inRandomOrder()->limit(4)->get(); 
		$banner 	= DB::table('petition_master')->where('is_banner' , '=' , '1')->inRandomOrder()->limit(8)->get();
		
		$total_signin 	= DB::table('users')->get();
		$total_signin 	= count($total_signin);
		$petition 		= DB::table('petition_master')->paginate($perpage);	
		
		$users			= array();
		$user_data   	= DB::table('users')->get();
		foreach($user_data as $val){
			$users[$val->id] 	= $val;
		}
		
		$petition_id_arr = array();
		if(!empty(Auth::user()->id))
		{
		$id 			= Auth::user()->id;
		$currentuser 	= User::find($id);
		$email 			= $currentuser->email;
		$sql = "select petition_id from `petition_user` where email = '".$email."'";
		$res = DB::select($sql);
		
		foreach($res as $val){
			$petition_id_arr[] = $val->petition_id;
		}
		}
		//echo '<pre>';print_r($petition_id_arr);exit;
		return view('frontend.homepage',compact('petition','petition_id_arr','victory','banner','page_type','total_signin','request','users'));
	}

	public function petitionlist(Request $request,$pageno)
	{
		$perpage = 3;
		//$pageno = 1;
		$startlimit = $perpage*$pageno;
		$sql = " select * from petition_master limit ".$startlimit.','.$perpage;
		$petition 		= DB::select($sql);
		//$petition 		= DB::table('petition_master')->paginate(2);	
		
		$users			= array();
		$user_data   	= DB::table('users')->get();
		foreach($user_data as $val){
			$users[$val->id] 	= $val;
		}
		
		$petition_id_arr = array();
		if(!empty(Auth::user()->id))
		{
			$id 			= Auth::user()->id;
			$currentuser 	= User::find($id);
			$email 			= $currentuser->email;
			$sql = "select petition_id from `petition_user` where email = '".$email."'";
			$res = DB::select($sql);
			
			foreach($res as $val){
				$petition_id_arr[] = $val->petition_id;
			}
		}

		$users			= array();
		$user_data   	= DB::table('users')->get();
		foreach($user_data as $val){
			$users[$val->id] 	= $val;
		}

		
	$i = 0;
	foreach($petition as $val){
$str = '<div class="col-sm-4">
<article class="box-light">
<header style="cursor:pointer;" onclick="javascript:window.location.href=\''.url('/campaign/details/'.$val->id).'\'">
<h3>'.$val->why_important.'</h3>
<div class="clear"></div>
<img class="img-responsive mg_tp_15" src="'.url($val->imageloc).'"> 
</header>
<p>'.$val->title.'</p>                                        
<p class="no_sig"> '.$val->total_signed.' signatures </p>                                                            
<p class=" text-info">By 
<a href="'.url('profile/campaigns/'.$val->user_id).'" style="cursor:pointer;">
'.$users[$val->user_id]->name.'</a>
</p>
'.((!empty($petition_id_arr) && in_array($val->id,$petition_id_arr))?'<button class="text-danger sgn_bottn">You already signed on this petition!</button>':'<button class="text-danger sgn_bottn" onclick="javascript:window.location.href=\''.url('/campaign/details/'.$val->id).'\'">Sign this petition!</button>').'
</article>
</div>';
echo $str;	
		$i++;
		if($i%3 == 0)
		{
			echo '<div class="clearfix"></div>';
		}
	}

	}
	
	public function campaign()
	{
		$petition = DB::table('petition_master')->get();
		return view('frontend.campaign',compact('petition'));
	}



	public function about()
	{
		$data = array();
		$data = DB::table('pages')->where('id','=',7)->get();
		foreach($data as $val)
		{
			
			$data['description'] = $val->description;
			
		}
		
		$description = $data['description'];
		return view('frontend.about',compact('description'));
	}



	public function campaign_details(Request $request , $petition_id  )
	{
		
		
		
		$data = array();
		
		$res = DB::table('petition_master')->where('id','=',$petition_id)->get();
		foreach($res as $val)
		{
			$data = $val;break;
		}
		$signed_cnt = 0;
		$res = DB::table('petition_user')->where('petition_id','=',$petition_id)->get();
		foreach($res as $val)
		{
			$signed_cnt++;
		}

		$signed_in_petition = '';		
		if($request->session()->has('signed_in_petition'))
		{	
		  $signed_in_petition = $request->session()->get('signed_in_petition');			
		  $request->session()->forget('signed_in_petition');		
	    }
		
		$user_details	= array();
		$user_data   	= DB::table('users')->where('id','=',$data->user_id)->get();
		foreach($user_data as $val){
			$user_details = $val;
		}
		
		
		
		$sql = "update petition_master set total_signed = '".$signed_cnt."' where id = '$petition_id' ";
				//echo $sql;exit;
	   	$res = DB::update($sql);
		
		
		$current_useremail = '';
		if(!empty(Auth::user()->id))
		{
			$id 				= Auth::user()->id;
			$currentuser 		= User::find($id);
			$current_useremail 	= $currentuser->email;
		}		
		
		$alreadysigned = 0;
		
		/********************************************/
		$sql = "select * from `petition_user` where email = '".$current_useremail."' and petition_id = ".$petition_id;
		$res = DB::select($sql);
		// echo "<pre>"; print_r($res);exit;
		/*for($alreadysigned=0;$alreadysigned<=1;$alreadysigned++)
		{}*/
		foreach($res as $val)
		{
		$alreadysigned = 1;break;
		}
		/***************************************/
				
		

		
		
		
		
				
		//echo '<pre>';print_r($user_details);exit;
		$page_type = 'petition_details';
		return view('frontend.petition_details',compact('alreadysigned','page_type','current_useremail','petition_id','data','signed_cnt','signed_in_petition','user_details'));
		//return view('frontend.edit_petition',compact('page_type','petition_id'));
	}

	public function media()
	{
	
		$media = DB::table('settings')->get();

		return view('frontend.media',compact('media'));
		//return redirect()->route('media')->with('message','Request Send Successfully ');
	}

	public function startapetition()
	{
		
		
		
		return view('frontend.start-a-petition');
		
		
	}
	
	
	
	
	
	public function startapetitionsubmit(Request $req)
	{		
		$arr = array();		
		//$id = $req->input('id');
				
		$arr['send_petition'] 	= addslashes($req->input('send_petition'));
		$arr['ask'] 			= addslashes($req->input('ask'));
		$arr['important'] 		= addslashes($req->input('important'));
		$req->session()->put('create_petition_arr',$arr);
		
		if(!Auth::check())
		{
			$req->session()->put('create_petition_loginerror',1);
			return redirect('/login');
		}		
		$arr['user_id'] 		= Auth::user()->id;
		
		$sql = "insert into petition_master set 
				user_id = ".$arr['user_id'].", 
				title = '".$arr['send_petition']."', 
				why_important = '".$arr['ask']."', 
				petition_letter = '".$arr['important']."' ";
				
				
		$res = DB::insert($sql);
		$last_id =    \DB::table('petition_master')->max('id');

		if($res)
		{

			//echo	$email_adderss;exit;
			$data = array('name'=>"$arr[send_petition]", "body" => "$arr[ask]");
			$ma = \Mail::send('emails.mail', $data, function($message) 
			{

				$mail_sql = "select * from users where id = '".Auth::user()->id."' " ;
				$mail_res = DB::select($mail_sql);
				foreach($mail_res as $val)
				{
					$email_adderss = $val->email;break ; //exit ;
				}

				$message->to( $email_adderss , '')
				->subject('You have successfully created Petition!');
				$message->from('support@69ideas.com','Admin');
			});
			//echo "petition created successfully"; exit ; 
		}
		
		
		
		
		
		
		
		//echo 875;exit;
		return redirect('campaign/edit/'.$last_id);

	}
	

	
	
	
	public function supportpetition(Request $req)
	{
		$petition_id 	= $req->input('petition_id');
		$firstname 		= $req->input('firstname');
		$lastname 		= $req->input('lastname');
		$country 		= $req->input('country');
		$email 			= $req->input('email');
		$zip 			= $req->input('zip');
		
		//$mail_title = $req->input('mail_title');$why_important = addslashes($request->input('why_important'));
		//echo $mail_title;exit;
		
		$sql1 = "insert petition_user set 
				petition_id = ".$petition_id.", 
				firstname = '$firstname', 
				lastname = '$lastname', 
				country = '$country', 
				email = '$email',
				zip = '$zip'";
		
		$res = DB::insert($sql1);
		
		$id = Auth::user()->id;
		//echo $id; exit ;
		 
		$data = array();
		$res = DB::table('users')->where('id','=',$id)->get();
		foreach($res as $val)
		{
			 $data = $val->email;break;
		}
		//print_r($data);
		//exit;
			$mydata['supporters_mail'] = $data;
		
		
			$mydata['subject'] 	= 'Thanks for signing my petition' ;
			$mydata['msg'] 		= '';
			$mydata['fname'] 	= $firstname;
			
			$mydata['mail_title'] = $req->input('mail_title');
			$mydata['url'] = str_replace('edit','details',$req->input('url'));
			
			 $mydata['ip'] = $_SERVER['REMOTE_ADDR'];
			 //echo $mydata['ip'] ; exit;
			 
			//echo $mydata['url'] ; exit;
			//echo url()->current(); exit;
			//echo "2222" .$mydata['mail_title'] ."111111";exit;
			
			//$mydata['emailid'] 	= $emailid;

			$data = array('name'=>'Admin', "body" => $mydata);
			$ma = \Mail::send('emails.suppotr_petition', $data, function($message) 
			{
				$mail_sql = "select * from users where id = '".Auth::user()->id."' " ;
				$mail_res = DB::select($mail_sql);
				foreach($mail_res as $val)
				{
					$email_adderss = $val->email;break ; //exit ;
				}
				
				//echo $email_adderss; exit ;

				//$message->to('prasenjit@kusmail.com','Admin')->subject('Contact Us');
				$message->to($email_adderss,'')->subject('Contact Us');
				$message->from('support@69ideas.com','Admin');
			});
		
		
			//echo 'mail sent ';exit;
		
		
		
	
		
		
		
		
		//$signed_in_petition = '';
		$req->session()->put('signed_in_petition', 'you have successfully sign in the petition!');
		//return redirect('campaign/details/'.$petition_id);
		return redirect('campaign/details/'.$petition_id);
	}
	
	
	
	public function startapetitionfromupdate(Request $request)
	{
		//print_r($_POST);exit;
		$id = $request->id;
		//echo '.......'.$id;exit;
		//$id = $request->input('id');
	
		
		if(!empty($request->file('file_img')))
		{
		$file_img = time().'_'.$request->file('file_img')->getClientOriginalName();	
     	$request->file('file_img')->move(base_path() . '/public/upload/petition/', $file_img);
		}
		
		//echo $file_img;exit;
		$title 	= $request->input('title');
		$title	= addslashes($title);
		$max_limit = $request->input('max_limit');
		$why_important = addslashes($request->input('why_important'));
		$petition_letter =  addslashes($request->input('petition_letter'));

		

		$sql = "update petition_master set  
       			title = '".$title."', 
				max_limit = '".$max_limit."',
				why_important = '".$why_important."', 
				petition_letter = '".$petition_letter."' ";
				if(!empty($file_img))
				{
				$sql .= ", imageloc = 'upload/petition/".$file_img."' ";
				}
	   			$sql .=" where id = '$id' ";
				//echo $sql;exit;
	   $res = DB::update($sql);
	   //echo "success";exit;
		return redirect('/');
	}
	
	
	
	
	
	
	public function campaign_edit(Request $req)
	{
		$data = array();
		$id = $req->id;
		$petition_id = $id;
		$res = DB::table('petition_master')->where('id','=',$id)->get();
		foreach($res as $val)
		{
			$data = $val;break;
		}
		
		$signed_cnt = 0;
		$res = DB::table('petition_user')->where('petition_id','=',$id)->get();
		foreach($res as $val)
		{
			$signed_cnt++;
		}
		
		$user_id 		= Auth::user()->id;
		$all_petition   = DB::table('petition_master')->where('user_id','=',$user_id)->get();
		
		$user_details	= array();
		$user_data   	= DB::table('users')->where('id','=',$user_id)->get();
		foreach($user_data as $val){
			$user_details = $val;
		}
		//echo '<pre>';print_r($data);exit;		
		
		
		$current_useremail = '';
		if(!empty(Auth::user()->id))
		{
			$id 				= Auth::user()->id;
			$currentuser 		= User::find($id);
			$current_useremail 	= $currentuser->email;
		}
		
		
		
		
		$alreadysigned = 0;
		
		/********************************************/
		$sql = "select * from `petition_user` where email = '".$current_useremail."' and petition_id = ".$petition_id;
		$res = DB::select($sql);
		 //echo "<pre>"; print_r($res);exit;
		/*for($alreadysigned=0;$alreadysigned<=1;$alreadysigned++)
		{}*/
		foreach($res as $val)
		{
		$alreadysigned = 1;break;
		}
		/***************************************/
				
		
		
		
					
		//echo '<pre>';print_r($user_details);exit;
		return view('frontend.edit_petition', compact('alreadysigned','data','current_useremail','all_petition','signed_cnt','petition_id','user_details'));		
	}
	
	
	
	
	
	
	
	
	
	
	public function partiqular_petition_dalete(Request $req)
	{
		//echo $id;exit;
		$id = $req->id;
		
		//echo $id;exit;
		
		$sql = "delete from petition_master where id = '$id'";
		
		
		//echo $sql;exit;
		$res = DB::delete($sql);
		return redirect('/');
	}
	
	public function mypetitions(){
		$user_id 		= Auth::user()->id;
		$all_petition   = DB::table('petition_master')->where('user_id','=',$user_id)->get();
		return view('frontend.mypetitions',compact('all_petition'));	
	}

	public function createpetitions(){
		return view('frontend.createpetitions');		
	}
	
	public function login(){	
		return view('frontend.login');
	}
	
	
	public function forgetpassword(){
		return view('frontend.forgetpassword');		
	}
	
	public function forgetpasswordsubmit(Request $req){
		echo $emailid = $req->emailid;
		exit;
		print_r($_POST);
	}

	public function profilecampaigns(Request $request)
	{
		$user_id = $request->id;
		$petition 		= DB::table('petition_master')->where('user_id',$user_id)->get();
		$profile 		= DB::table('users')->where('id',$user_id)->get();
		$profiledetails	= array();
		foreach($profile as $val){
			$profiledetails	= $val;
		}
		//print_r($profiledetails);exit;
		return view('frontend.profilecampaign',compact('petition','profiledetails'));
	}
	
	
	public function guidline()
	{
		//echo "ggggggggggg";exit;
		$guid = DB::table('faq')->get(); 
		
		return view('frontend.guidline',compact('guid'));		
	}
	
	public function fullguidline(Request $id)
	{
		//echo "ggggggggggg";exit;
		
		 $q_id = $id->id;
		
		
		$full_guid = DB::table('faq')->where('id',$q_id)->get(); 
		
		return view('frontend.full_guidline',compact('full_guid'));		
	}	
	/*public function campaign_view()
	{
		echo "view page";exit;
		
		$id = 115;
		$res = DB::table('petition_master')->where('id','=',$id)->get();
		return view('frontend.edit_petition', compact('res'));
		
		
		$campaignview = DB::table('petition_master')->where('user_id','=','115')->get();
		return view('frontend.campaign',compact('campaignview'));
		
		
		
	}*/
	public function media_page_form()
	{
		if(!empty($_POST['emailid']) && $_POST['emailid'] != 'jhoncronie123@gmail.com')
		{
			//echo '<pre>';print_r($_POST);exit;
			$fname 		= $_POST['fname'];
			$emailid 	= $_POST['emailid'];
			$subject 	= $_POST['subject'];
			$msg 		= $_POST['msg'];
			
			
			$mydata['subject'] 	= $subject;
			$mydata['msg'] 		= $msg;
			$mydata['fname'] 	= $fname;
			$mydata['emailid'] 	= $emailid;
			
			$data = array('name'=>'Admin', "body" => $mydata);
			$ma = \Mail::send('emails.contactus', $data, function($message) 
			{
				$message->to('prasenjit@kusmail.com','Admin')->subject('Demo Contact Us');
				//$message->to('support@69ideas.com','Admin')->subject('Contact Us : '.$subject);
				$message->from('support@69ideas.com','Admin');
			});
			//echo 'mail sent ';exit;
		}



		$fname 		= $_POST['fname'];
		$emailid 	= $_POST['emailid'];
		$subject 	= $_POST['subject'];
		$msg 		= $_POST['msg'];
		
		
		$mydata['subject'] 	= $subject;
		$mydata['msg'] 		= $msg;
		$mydata['fname'] 	= $fname;
		$mydata['emailid'] 	= $emailid;
		
		$data = array('name'=>'Admin', "body" => $mydata);
		$ma = \Mail::send('emails.contactus', $data, function($message) 
		{
			//$message->to('prasenjit@kusmail.com','Admin')->subject('Contact Us');
			$message->to('support@69ideas.com','Admin')->subject('Contact Us');
			$message->from('support@69ideas.com','Admin');
		});


		
		return redirect()->route('media')->with('message','Request Send Successfully ');
	}
	
	
	

}
