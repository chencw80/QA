<h3 class="post_headings">Featured Posts
    @if(count($featuredPosts) > 5)
        <a href="{{ route('filterFeatured')}}" class="more pull-right">More ></a>
    @endif
</h3>
<div class="featured_post_block clearfix">
    <div class="row">
        @if(count($featuredPosts) > 0)
            @foreach($featuredPosts as $row)
                <!-- 20181122 Changed by SHC PL:20181119_CP_022 
                <div class="col-md-2 col-sm-3 col-xs-6 post" id="product-{{$row['id']}}">-->
                <div style="padding: 5px 5px 5px 5px" class="col-md-2 col-sm-3 col-xs-6 post" id="product-{{$row['id']}}">
                    <!-- 20181122 Changed by SHC PL:20181119_CP_022 
                    <div style="border: 1px solid #ccc;padding: 0px 10px;background-color:  #ffffff;">-->
                    <div class="zoom" style="border: 0px solid #ccc;padding:  5px 5px 5px 5px;background-color:  rgb(255,255,235);">
                        <div class="img_wrapper">
                            @foreach($row['post_image'] as $pic)
                                {{-- @if($row['post_type'] == 1)
                                    <span class="featured-image">&nbsp;</span>
                                @endif --}}
                                @if(array_key_exists('getPostPremiumItems', $row))
                                    @foreach($row['getPostPremiumItems'] as $postPremiumItem)
                                        @if($postPremiumItem['premium_id'] == 8 && $postPremiumItem['status'] != 2)
                                            <span class="urgent-image">&nbsp;</span>
                                        @endif
                                    @endforeach
                                @endif

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
                                <!-- 20181122 Changed by SHC PL:20181119_CP_022 
                                <p class="post_price" style="font-size: 16px; color:#8B0304; font-weight: bold; margin-top: 0px;">RM {{ ($row['price']) }}</p>-->
                                <p class="post_price" style="font-size: 14px; color:#8B0304; font-weight: bold; margin-top: 0px;">RM {{ ($row['price']) }}</p>
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
</div>
{{-- Featured Post Section --}}
<h3 class="post_headings recommended_headings">Recommended For You 
    @if(count($normalPosts) > 5)
        <a href="{{ route('filterRecommended')}}" class="more pull-right">More ></a>
    @endif
</h3>
<div class="featured_post_block clearfix">
<div class="row">
    @if(count($normalPosts) > 0)
        @foreach($normalPosts as $row)
        <!-- 20181122 Changed by SHC PL:20181119_CP_022 
        <div class="col-md-2 col-sm-3 col-xs-6 post" id="product-{{$row['id']}}"> -->
        <div style="padding: 5px 5px 5px 5px" class="col-md-2 col-sm-3 col-xs-6 post" id="product-{{$row['id']}}">
            <!-- 20181122 Changed by SHC PL:20181119_CP_022 
            <div style="border: 1px solid #ccc;padding: 0px 10px;background-color:  #ffffff;">-->
            <div class="zoom" style="border: 0px solid #ccc;padding: 5px 5px 5px 5px;background-color:  rgb(255,255,235);">
                <div class="img_wrapper">
                    @foreach($row['post_image'] as $pic)
                    {{-- @if($row['post_type'] == 1)
                        <span class="featured-image">&nbsp;</span>
                    @endif --}}
                    @if(array_key_exists('getPostPremiumItems', $row))
                        @foreach($row['getPostPremiumItems'] as $postPremiumItem)
                            @if($postPremiumItem['premium_id'] == 8 && $postPremiumItem['status'] != 2)
                                <span class="urgent-image">&nbsp;</span>
                            @endif
                        @endforeach
                    @endif

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
                    <!-- 20181122 Changed by SHC PL:20181119_CP_022 
                    <p class="post_price" style="font-size: 16px; color:#8B0304; font-weight: bold; margin-top: 0px;">RM {{ ($row['price']) }}</p>-->
                    <p class="post_price" style="font-size: 14px; color:#8B0304; font-weight: bold; margin-top: 0px;">RM {{ ($row['price']) }}</p>
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
        <p>No Recommended Post found.</p>
    </div>
    @endif
</div>
</div>