@php
	$postlocations = [];
	foreach ($location as  $loc) {
		$postlocations[$loc['id']] = $loc['name']; 
	}
	//$featured_post_collection = [];
	//$normal_post_collection = [];
	// if (!Request::is('filter_data')) {
	//$collect = $data->groupBy('post_type');
	//$collect = $collect->toArray();
	// if (array_key_exists('1', $collect)) {
	// 		$featured_post_collection = $collect[1];
	// }
	// if (array_key_exists('0', $collect)) {					
	// 		$normal_post_collection = $collect[0];
	// }

	$searchResultText = "All";

	if(!is_null($searchstring))
	{
		$searchResultText = $searchstring;
	}
	else if(!is_null($selectedLoc))
	{
		$searchResultText = $postlocations[$selectedLoc];
	}
	else
	{
		foreach($category as $cat)
		{
			if($cat->id == $selected)
			{
				$searchResultText = $cat->short_name;
			}
		}
	}
	// }

		// dd($featured_post_collection);
@endphp
@extends('layouts.app')

@section('contentSearch')<!-- 20181011 Changed by SHC move search keyword session to SearchTop-->
<div class="search_filter">
		<form method="post" id="searchFormId" name="browseFrom" action="{{URL::to('filter_data')}}" >
				<div class="col-12">	
					<div class="row">
						
						<div class="cili-searchbar flex">
							<div class="search-flex">
								<div class="flex">

									<div class="category-slot category">
										<select name="category" id="category">
											<option value="">{{__('header.Select Category')}}</option>
											@foreach($category as $cat)
											@if($cat->id==$selected)
											
											<option value="{{$cat->id}}" selected="selected" >{{$cat->short_name}}</option>
											@else
											<option value="{{$cat->id}}">{{$cat->short_name}}</option>

											@endif
											@endforeach
										</select>
									</div>
									
									<div class="search-slot searcharea">
										<div class="col-md-12">
											<input type="text" class="searchkeyword" id="searchkeyword" placeholder="{{__('header.Search')}}" value="{{$searchstring}}" name="searchkeyword" />
										</div>
										{{-- <div class="row">
											<div class="col-md-6">
												<input type="text" id="fromprice" value="" name="fromprice" placeholder="{{__('header.From Price')}}">
											</div>
											<div class="col-md-6">
												<input type="text" id="toprice" value="" name="toprice" placeholder="{{__('header.To Price')}}">
											</div>
										</div> --}}
									</div>

									<div class="location-slot location">
										<select name="location" id="location">
											<option value="">{{__('header.Location')}}</option>
											@foreach($location as $loc)
												@if($loc['id']==$selectedLoc)
													<option value="{{$loc['id']}}" selected="selected" >{{$loc['name']}}</option>
												@else
													<option value="{{$loc['id']}}">{{$loc['name']}}</option>
												@endif
											@endforeach
										</select>
									</div>

									<div class="icon-slot submit">
										<input type="hidden" name="_token" value="{{csrf_token()}}" />
										<input id="SubmitForm" type="submit" name="submit" value="{{__('header.Submit')}}" />
									</div>

								</div>
							</div>

							<div class="post-flex">
								<!-- 20181012 Add by SHC move create post to search session-->
								<div class="post_free_ads">
									@if(!Auth::guest())
										@if(Auth::user()->type == 1)
										@if(Auth::user()->user->free_post > 0)
											<a href="{{ route('postCreation', ['scId' => 1, 'id' => 0]) }}" style="font-weight: bold;"> {{__('header.Create Free Post')}}</a>
										@else
											<a href="{{ route('postCreation', ['scId' => 1, 'id' => 0]) }}" style="font-weight: bold;"> {{__('header.Create Post')}}</a>
										@endif
										@else
										<a href="{{ route('postCreation', ['scId' => 1, 'id' => 0]) }}" style="font-weight: bold;">{{ __('header.Create Post')}}</a>
										@endif
									@else
										<a href="#" data-toggle="modal" data-target="#loginModal" style="font-weight: bold;">{{ __('header.Create Free Post')}}</a>
									@endif
								</div>
							</div>

							<div>
								{{-- <label style="color: white;">{{__('header.Search Specific item')}}:</label> --}}
							</div>
						</div>

					</div>
				</div>
		</form>
</div>

@endSection <!-- 20181011 Changed by SHC move search keyword session to SearchTop-->

@section('content')<!-- 20181011 Changed by SHC move search keyword session to SearchTop-->
<div class="container" id="homepost">
	{{-- {{$data->appends(Request::except(['_token', 'submit']))->links()}} --}}
	<p id="priceError" style="color: red;"></p>
	@if($status = Session::get('browseMessage'))
	<div class="alert alert-success">
		{{$status}}
	</div>
	@endif
	
	@if($accountAdminError = Session::get('accountAdminError'))
      <div class="text-center alert alert-danger homeError">{{$accountAdminError}}</div>
    @endif
	<div id="error_message" style="display: none;">
	</div>

	@if(!Request::is('filter_data'))

<!-- 20181011 Remarked by SHC temporary hide NGO
		{{-- NGO Category Section --}}
		<div id="ngocat">
			<h2 class="ngo_heading">Support These NGOs <a href="{{route('viewAllNgos')}}" class="more pull-right">more ></a></h2>
			<div class="row ngo-slider">	
				@php
					$i=1;
				@endphp
				@foreach($activeNgoCategories->toArray() as $ngoCat)
					<div class="col-md-2">
						<a href="{{route('ngoCateogryDetails', ['categoryid' => $ngoCat['id']])}}">
							<img class="img-responsive postimg" src="{{ asset('images/'.$ngoCat['ngo_category_url']) }}" alt="" />
						</a>
						<h3 class="ngo_title"><a href="{{route('ngoCateogryDetails', ['categoryid' => $ngoCat['id']])}}" style="color: #939598;">{{ $ngoCat['category_name'] }}</a></h3>
					</div>
				@php
					$i++;
					if ($i == 5) {
							$i=1;
					}	
				@endphp		
				@endforeach
			</div>
		</div>-->
		{{-- /NGO Category Section --}}
		@include('home')
	@else

	{{-- <div class="col-md-11"><p>{{__('header.Total Result:')}} {{$count}}</p></div> --}}
	{{-- @if(count($data)==0)
		<p style="text-align: center">{{__('header.No result found')}}</p>
	@endif --}}
	@php
		$i=1;
	@endphp
	<div class="row">
		<div class="col-md-12">
			<h3 class="post_headings">Featured 
				@if(count($featuredPosts) > 5)
					<a href="{{ route('filterFeatured')}}" class="more pull-right">more ></a>
				@endif
			</h3>
		</div>
	</div>
	<div class="row" style="border-bottom: solid 2px #424242;padding-bottom: 20px; margin-bottom: 20px;">
        @if(count($featuredPosts) > 0)
            @foreach($featuredPosts as $row)
        		<!-- 20181122 Changed by SHC PL:20181119_CP_022 
	            <div class="col-md-2 col-xs-3 post" id="product-{{$row['id']}}">-->
	            <div style="padding: 5px 5px 5px 5px" class="col-md-2 col-xs-3 post" id="product-{{$row['id']}}">
        		<!-- 20181122 Changed by SHC PL:20181119_CP_022 
	            	<div style="border: 1px solid #ccc;padding: 0px 10px;background-color:  #ffffff;">-->
	            	<div  class="zoom" style="border: 0px solid #ccc;padding:  5px 5px 5px 5px;background-color:  rgb(255,255,235);">
	                    <div class="img_wrapper">
	                        @foreach($row['post_image'] as $pic)
	                        {{-- @if($row['post_type'] == 1)
	                            <span class="featured-image">&nbsp;</span>
	                        @endif --}}
	                        {{-- @if(array_key_exists('getPostPremiumItems', $row))
	                        @foreach($row['getPostPremiumItems'] as $postPremiumItem)
	                            @if($postPremiumItem['premium_id'] == 8)
	                                <span class="urgent-image">&nbsp;</span>
	                            @endif
	                        @endforeach
	                        @endif --}}
	                        @php
		                        if($row['user_id'] == $userid)
		                        {
		                        	$imgUrl = route('mypostdetail', ['id' => $row['id'], 'ps' => 0]);
		                        }
		                        else
		                        {
		                        	$imgUrl = route('item_detail', ['id' => $row['id']]);
		                        }
	                        @endphp
	                        <div class="smallImageThumbnailsDiv">
		                        <a href="{{$imgUrl}}">
		                        	<img border="1" class="postimg smallImageThumbnailImg" data-lazy-src="data:image/jpeg;base64,<?php echo base64_encode($pic['pic_image']); ?>" />
		                        </a>
		                    </div>
	                        @break
	                        @endforeach
	                    </div>
	                    <div class="row">
			                <div class="col-md-12">
			                    @if($row['user_id'] == $userid)
			                    <a class="post_title h-32" href="{{route('mypostdetail', ['id' => $row['id'], 'ps' => 0])}}">
			                    	@php
		                                $length = 28;
		                            @endphp
				                    @foreach($row['getPostPremiumItems'] as $postPremiumItem)
										@if($postPremiumItem['premium_id'] == 8 && $postPremiumItem['status'] != 2)
											<span style="color:red;">[Urgent]</span>
											@php
				                                $length = 20;
				                            @endphp
										@endif
									@endforeach
			                        {{str_limit($row['short_description'], $length)}}
			                    </a>
			                    @else
			                    <a class="post_title h-32" href="{{route('item_detail', ['id' => $row['id']])}}">
			                    	@php
		                                $length = 28;
		                            @endphp
			                    	@foreach($row['getPostPremiumItems'] as $postPremiumItem)
										@if($postPremiumItem['premium_id'] == 8 && $postPremiumItem['status'] != 2)
											<span style="color:red;">[Urgent]</span>
											@php
				                                $length = 20;
				                            @endphp
										@endif
									@endforeach
			                        {{str_limit($row['short_description'], $length)}}
			                    </a>
			                    @endif
			                </div>
		                    <div class="col-md-12">
		                        <p class="post_price" style="font-size: 16px; color:#8B0304; font-weight: bold; margin-top: 0px;">RM {{ ($row['price']) }}</p>
		                    </div>
		                    <div class="col-md-12 text-right">
		                        <p style="color: #231f20;font-size: 13px;">{{ $postlocations[$row['state']] }}</p>
		                    </div>
	                    </div>
	                </div>
	            </div>
            @endforeach
        @else
        <div class="col-md-12">
            <p>No Featured Post found.</p>
        </div>
        @endif
     </div>
	<div class="row">
		<div class="col-md-12">
			<h3 class="post_headings">{{ count($normalPosts) }} items found for {{$searchResultText}}
				@if($normalPosts->count() > 11)
					<a href="{{ route('filterRecommended')}}" class="more pull-right">more ></a>
				@endif
			</h3>
		</div>
	</div>

	<div class="row">
		@foreach($normalPosts as $row)   
        <!-- 20181122 Changed by SHC PL:20181119_CP_022         
		<div class="col-md-2 col-xs-3 post" id="product-{{$row['id']}}">     -->	
		<div style="padding: 5px 5px 5px 5px" class="col-md-2 col-xs-3 post" id="product-{{$row['id']}}">
        	<!-- 20181122 Changed by SHC PL:20181119_CP_022 
			<div style="border: 1px solid #ccc;padding: 0px 10px;background-color:  #ffffff;margin-bottom:	20px;">-->
	        <div  class="zoom" style="border: 0px solid #ccc;padding:  5px 5px 5px 5px;background-color:  rgb(255,255,235);margin-bottom:	20px;">
				<div class="img_wrapper">
					@foreach($row['post_image'] as $pic)
					{{-- @if($row['post_type'] == 1)
						<span class="featured-image">&nbsp;</span>
					@endif --}}
					{{-- @if(array_key_exists('getPostPremiumItems', $row))
					@foreach($row['getPostPremiumItems'] as $postPremiumItem)
						@if($postPremiumItem['premium_id'] == 8)
							<span class="urgent-image">&nbsp;</span>
						@endif
					@endforeach
					@endif --}}
					@php
	                    if($row['user_id'] == $userid)
	                    {
	                    	$imgUrl = route('mypostdetail', ['id' => $row['id'], 'ps' => 0]);
	                    }
	                    else
	                    {
	                    	$imgUrl = route('item_detail', ['id' => $row['id']]);
	                    }
	                @endphp
	                <div class="smallImageThumbnailsDiv">
		                <a href="{{$imgUrl}}">
							<img border="1" class="postimg smallImageThumbnailImg" data-lazy-src="data:image/jpeg;base64,<?php echo base64_encode($pic['pic_image']); ?>" />
						</a>
					</div>
					@break
					@endforeach
					{{-- @if($row->user_id == $userid)
					<div class="action_links">
						<div class="col-xs-6 text-center">
								<span id="{{$row->id}}Delete" data="{{$row->id}}" title="{{__('header.Delete')}}" class="glyphicon glyphicon-trash Delete"></span>
						</div>
						<div class="col-xs-6 text-center">
							<a href="{{ route('editPostAd', ['id'=>$row->id])}}" title="{{__('header.Edit')}}">  
								<span class="glyphicon glyphicon-pencil"></span>
							</a>	
						</div>
					</div>
					@endif --}}
				</div>
				{{-- <div>
					@if($row->user_id !== $userid)
					@if(count($row->getwishlist) == null && (!in_array($row->id, $myCookie)))
						<button class="like addwishlist" id="{{$row->id}}" data="{{$row->id}}" title="{{__('header.Add to wishlist')}}"></button>
					@else
						<button class="like removewishlist" id="{{$row->id}}" data="{{$row->id}}" title="{{__('header.Remove from wishlist')}}"></button>
					@endif
					@endif			
				</div> --}}
				<div class="row">
					<div class="col-md-12">
						@if($row['user_id'] == $userid)
							<a class="post_title h-32" href="{{route('mypostdetail', ['id' => $row['id'], 'ps' => 0])}}">
								@php
									$length = 28;
								@endphp
								@foreach($row['getPostPremiumItems'] as $postPremiumItem)
									@if($postPremiumItem['premium_id'] == 8 && $postPremiumItem['status'] != 2)
										<span style="color:red;">[Urgent]</span>
										@php
											$length = 20;
										@endphp
									@endif
								@endforeach
								{{str_limit($row['short_description'], $length)}}
							</a>
						@else
							<a class="post_title h-32" href="{{route('item_detail', ['id' => $row['id']])}}">
								@php
									$length = 28;
								@endphp
								@foreach($row['getPostPremiumItems'] as $postPremiumItem)
									@if($postPremiumItem['premium_id'] == 8 && $postPremiumItem['status'] != 2)
										<span style="color:red;">[Urgent]</span>
										@php
											$length = 20;
										@endphp
									@endif
								@endforeach
								{{str_limit($row['short_description'], $length)}}
							</a>
						@endif
					
						{{-- @foreach($row->getuserdetail as $username)
						@if($username->account_type !== 1)
						<a class="post_author" href="{{route('infoDetails', ['id' => $username->user_id])}}">
							by: {{$username->first_name}}
						</a>
						@endif
						@endforeach --}}
						{{-- <p class="post_meta">
						<span class="price"><strong>{{__('header.Credits:')}} </strong> {{$row->price}}</span>
						@if($row->new_used == 0)
							 <span><strong>type:</strong>{{__('header.Second Hand(Used)')}}</span>
						@else
							<span><strong>type: </strong>{{__('header.New')}}</span>
						@endif
						@if($row->can_negotiate == 0)
							<span>{{__('header.Not Negotiable')}}</span>
						@else
							<span>{{__('header.Negotiable')}}</span>
						@endif
						@php
							$category = [];
							foreach ($row->post_category as $cat) {
								$category[] = $cat->short_name;
							}
						@endphp
						<span><strong>categories:</strong>{{implode(',',$category)}}</span>
						</p> --}}
					
					</div>
					<div class="col-md-12">
						<p class="post_price" style="font-size: 16px; color:#8B0304; font-weight: bold; margin-top: 0px;">RM {{ ($row['price']) }}</p>
					</div>
					<div class="col-md-12 text-right">
						<p style="color: #231f20;font-size: 13px;">{{ $postlocations[$row['state']] }}</p>
					</div>
				</div>
			</div>
		</div>
		@php
			$i++;
			if ($i >6) {
				echo '<div class="clearfix"></div>';
				$i=0;
			}
		@endphp
		@endforeach
	</div>	
	<div class="row">
		{{-- <div class="col-md-12 text-right">
			{{$data->appends(Request::except(['_token', 'submit']))->links()}}
		</div> --}}
		<div class="modal fade" id="myModal" role="dialog">
		   <div class="modal-dialog">
		      <!-- Modal content-->
		      <div class="modal-content">
		         <div class="modal-header">
		            <button type="button" class="close" data-dismiss="modal">&times;</button>
		            <h4 class="modal-title">{{__('header.Delete Post')}}</h4>
		         </div>
		         <div class="modal-body">
		            <form method="post" id="deleteForm" action="#" >
		               <input type="hidden" name="_token" value="{{csrf_token()}}">
		               <input type="hidden" id="pid" name="pid" value="">
		               <label style="margin-left: 5%;margin-right: 5%">{{__('header.Sure Delete')}}?</label>
		               <input id="DeleteBtn" type="button" class="btn btn-danger" value="Delete" style= "width: 20%;margin-right:40%;margin-left: 40%;margin-top: 10px;margin-bottom: 2px">
		            </form>
		         </div>
		      </div>
		   </div>
	  	</div>
		</div>	
@endif	

@include('layouts.ad_banner')
</div>
 {{-- container --}}
	
	

<script type="text/javascript">
	$(document).ready(function(){
    		$.DrLazyload(); //Yes! that's it!
    		$('.homeError').delay(4000).fadeOut('slow');
    	});
</script>

<script type="text/javascript">
	$(document).ready(function() 
	{
		$('#fromprice, #toprice').inputmask({ regex: "\\d{0,15}" });
		$(".like").click(function()
		{
			var postid = $(this).attr('data');		
			$.ajax({
				url: "/likepost",
				type:'POST',
				data: {'id':postid},
				headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
				success: function(data) {
					if(data.status=='error')
					{
						$('#error_message').html(data.loginlink);
						$('#error_message').show();
					}
					myFunction(data,postid);
				},error: function(){

				}});
		});

		$(".Delete").click(function()
         {
            var id = $(this).attr('data');
            $('#myModal #pid').val(id);
            $('#myModal').modal('show');
         });

		$(document).on('click', '#DeleteBtn', function(e){
			e.preventDefault();
			$('#myModal').modal('hide');
			var val = $('#myModal #pid').val();

			window.location.href = '{{URL::to('deletePost')}}/'+val;
		});

		$(document).on('click', '#SubmitForm', function(e){
			var fromPrice = $('#fromprice').val();
			var toPrice = $('#toprice').val();

			if(fromPrice && toPrice)
			{
				var fromPrice = parseInt($('#fromprice').val());
				var toPrice = parseInt($('#toprice').val());
				if(fromPrice >= toPrice)
				{
					$('#priceError').fadeIn();
					document.getElementById("priceError").innerHTML="To price should be greater than from price";
					$('#priceError').delay(4000).fadeOut('slow');
					return false;
				}
			}

			$('#searchFormId').submit();
		});

		$("#toprice").click(function() 
		{
			var fromPriceValue = $("#fromprice").val();
			$("#toprice").val(fromPriceValue);
		});
	});

	function myFunction(data,postid) 
	{
		var property = $('#'+postid);

		if(data==0)
		{
			@if(Route::current()->uri === "wishlist")
			$('#product-'+postid).hide();
			@endif
			// property.html('Add to wishlist');
			// property.css('background-color', "#FFFFFF");
			property.removeClass('removewishlist');
			property.addClass('addwishlist');
		}
		if(data==1)
		{
			// property.html('Remove from wishlist');
			property.removeClass('addwishlist');
			property.addClass('removewishlist');
			// property.css('background-color', "#D62C24");
		}
	}
</script>

<script type="text/javascript">

	$(document).ready(function()
	{
		$('#sucessmsg').delay(4000).fadeOut('slow');
	});
</script>
@endsection