
@extends('layouts.frontend')
@section('content')


<div class="infinite-scroll">

<div id="teaser-boxes" class="ajaxpetitionlist container_12">

	<?php 
		$i = 0;
		foreach($petition as $val)
		{ 
			$i++;
	?>
			<div class="col-sm-4">
				<article class="box-light">
					<header style="cursor:pointer;" onClick="javascript:window.location.href='<?php echo url('/campaign/details/'.$val->id);?>'">
						<h3> <?php echo $val->why_important; ?>
						
						
						</h3>
                        
						<div class="clear"></div>
						<img class="img-responsive mg_tp_15" src="<?php echo url($val->imageloc);?>"> 
					</header>
					<p><?php echo $val->title; ?>
					
					
					</p>                                        
					<p class="no_sig"> <?php echo $val->total_signed; ?> signatures </p> 
                     
                     <p class=" text-info"> updated on : <?php echo date('dS-F-Y',strtotime($val->addedon)); ?> </p>
                                                                               
					<p class=" text-info">By 
                    	<a href="<?php echo url('profile/campaigns/'.$val->user_id);?>" style="cursor:pointer;">
                        	<?php 
								echo $users[$val->user_id]->name;//exit;
							?>
                        </a>
                    </p>
					<?php 
                        if(!empty($petition_id_arr) && in_array($val->id,$petition_id_arr))
                        {
					?>
							<button class="text-danger sgn_bottn">You already signed on this petition!</button>
                    <?php
						}
						else
						{
					?>
							<button class="text-danger sgn_bottn" onClick="javascript:window.location.href='<?php echo url('/campaign/details/'.$val->id);?>'">Sign this petition!</button>
                    <?php
						}
                    ?>                    
				</article>
			</div>
	<?php 
			if($i%3 == 0)
			{
				echo '<div class="clearfix"></div>';
			}
		}
	?>
    
  
    </div>
</div>

</div>
    <script src="<?php echo url('/frontend/js/jquery.jscroll.min.js');?>"></script>
    
    
    <script type="text/javascript">
        $('ul.pagination').hide();
        $(function() {
            $('.infinite-scroll').jscroll({
                autoTrigger: true,
                loadingHtml: '<img class="center-block" src="/images/loading.gif" alt="Loading..." />', // MAKE SURE THAT YOU PUT THE CORRECT IMG PATH
                padding: 0,
                nextSelector: '.pagination li.active + li a',
                contentSelector: 'div.infinite-scroll',
                callback: function() {
                    $('ul.pagination').remove();
                }
            });
        });
    </script>
    
@endsection
