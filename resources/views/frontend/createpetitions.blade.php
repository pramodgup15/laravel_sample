@extends('layouts.frontend')
@section('content')


<div  class="container container_12 ">





  <form id="modal_header" class="form-horizontal" action="{{ route('startapetitionsubmit') }}" method="post" name="myform1" onsubmit="return validateForm()">
    {{ csrf_field() }}
    <div class="stp_mdl">
    
        <div class="modal-header strt_pt" style="padding-bottom:2px;">
         
          <h4 class="modal-title ">Create my petition</h4>
          <h5>Your campaign for change starts here!</h5>
        </div>
        <div class="modal-body pti_str">
          <p>
            <label >1. To whom do you want to send your petition? <span>*</span></label>
            <input type="text" id="" name="send_petition" class="form-control form-control-sm" required>
          </p>
          <h5>Person, organization, administration</h5>
          <p>
            <label >2. What do you want to ask? <span>*</span></label>
            <input type="text" id="" name="ask" class="form-control form-control-sm" required>
          </p>
          <h5>The petition you want to create</h5>
          <p>
            <label >3. Why is it important?  <span>*</span></label>
            <textarea  name="important" cols="" rows="4" class="form-control form-control-sm ckeditor" required></textarea>
          </p>
          <h5> Explain your reasons and why they should support your petition</h5>
          <p>
            
          </p>
          
          
          <span class="msg-error error"></span>
          
          <div class="g-recaptcha" data-sitekey="6LcGeVEUAAAAAFaFQmZ97vNgFHmgRjZlNyLyjld9"></div>
        </div>
        <div class="modal-footer" style="padding-top:2px;">
            <input class="btn btn-success pull-left" style=" text-transform: uppercase; font-weight: bold; float:left;"  type="submit" id="edit-submit" name="op" value="Start my petition">
        </div>
        <br />
    </div>
  </form>



</div>


<script>
$( '#edit-submit' ).click(function(){
  var $captcha = $( '#recaptcha' ),
      response = grecaptcha.getResponse();
  
  if (response.length === 0) 
  {
    $( '.msg-error').text( "reCAPTCHA is mandatory" );
    if( !$captcha.hasClass( "error" ) )
	{
      $captcha.addClass( "error" );
    }
	return false;
  } 
  return true;
})
</script>


@endsection
