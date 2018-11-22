@extends('laravel-authentication-acl::admin.layouts.base-2cols')
@section('title')
Admin area: Ngo User
@stop
@section('content')

<div class="col-md-12 col-sm-12 col-xs-12">
    <h3>NGO</h3>
    <hr>
</div>

<div class="col-md-12">

  <div class="row clearfix">
    @if(Session::has('Status'))
      <p class="alert alert-info">{{ Session::get('Status') }}</p>
    @endif
  </div>

  <form method="post" action="{{URL::to('admin/filterNgo')}}" >
    <div class="row">
      <div class="form-group">
        <div class="col-md-3">
          <select class="form-control" name="Ngo_status">
            @if($ngo_status==0)
              <option value="0" selected="selected">Created</option>
            @else
              <option value="0">Created</option>
            @endif
            @if($ngo_status==1)
              <option value="1" selected="selected">Approved</option>
            @else
              <option value="1">Approved</option>
            @endif
          </select>
        </div>
        <div class="col-md-3">
          <input type="hidden" name="_token" value="{{csrf_token()}}">
          <input type="submit" class="btn btn-info" name="submit" value="{{__('header.Submit')}}">
        </div>
      </div>
    </div>
  </form>


  <div id="move">
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th width="50%">
          Email
          </th>
          <th width="50%">
          Action
          </th>
        </tr>
      </thead>
      <tbody>
        @if($ngo->count() > 0)
          @foreach($ngo as $row)
            <tr>
              @if($ngo_status==0)
                <td>
                  {{$row->email}}
                </td>
                <td>
                  @if($appUser->id  !== $row->first_approval)
                    <a class="btn btn-info" href="{{ route('statusapprovedNgo', ['id' => $row->id , 'account_actived'=>1])}}">Approve</a> 
                    <a class="Reject btn btn-info" id="{{$row['id']}}" data="{{$row->id}}" >Reject</a>
                    <a class="btn btn-info" href="{{ URL::to('getCert/'.$row->company_certificate_url)}}" target="_blank">View Document</a>
                    <!--20181025 Change by SHC 20180816_CP_002 Start -->                   
                    @if(!is_null($row->company_certificate_url2))
                      <a class="btn btn-info" href="{{ URL::to('getCert/'.$row->company_certificate_url2)}}" target="_blank">View Document2</a>  
                    @endif             
                    @if(!is_null($row->company_certificate_url3))
                      <a class="btn btn-info" href="{{ URL::to('getCert/'.$row->company_certificate_url3)}}" target="_blank" style="text-align:center;display:inline-block;margin:auto 0;">View Document3</a>  
                    @endif
                    <!--20181025 Change by SHC 20180816_CP_002 End --> 
                    @if($row->flag == 0)
                      <a class="btn btn-info" href="{{ route('flagstatus', ['id' => $row->id , 'flag'=>1])}}" style="text-align:center;display:inline-block;margin:auto 0;">Flag</a>
                    @elseif($row->flag == 1)
                      <a class="btn btn-info" href="{{ route('unflagstatus', ['id' => $row->id , 'unflag'=>0])}}" style="text-align:center;display:inline-block;margin:auto 0;">Un Flag</a>
                    @endif
                  @elseif($appUser->id  == $row->first_approval)
                    @if($row->flag == 0)
                      <a class="btn btn-info" href="{{ route('flagstatus', ['id' => $row->id , 'flag'=>1])}}">Flag</a>
                    @elseif($row->flag == 1)
                      <a class="btn btn-info" href="{{ route('unflagstatus', ['id' => $row->id , 'unflag'=>0])}}">Un Flag</a>
                    @endif
                  @endif
                </td>
              @endif
              @if($ngo_status==1)
                <td>
                  {{$row->email}}
                </td>
                <td>
                  @if($row->flag == 0)
                    <a class="btn btn-info" href="{{ route('flagstatus', ['id' => $row->id , 'flag'=>1])}}">Flag</a>
                  @elseif($row->flag == 1)
                    <a class="btn btn-info" href="{{ route('unflagstatus', ['id' => $row->id , 'unflag'=>0])}}">Un Flag</a>
                  @endif
                </td>
              @endif
            </tr>
          @endforeach
        @else
          <tr>
            <td colspan="2">
              No Record Found
            </td>
          </tr>
        @endif
      </tbody>
    </table>
  </div>
           
  <div class="modal fade" id="rejectModal" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">{{__('header.Reject Reason')}}</h4>
        </div>
        <div class="modal-body">
          <form method="post" id="rejectForm" action="#">
            <input type="hidden" name="_token" value="{{csrf_token()}}">
            <input type="hidden" id="id" name="id" >
            <div class="row">
              <div class="col-md-12">
                <div class="form-group clearfix">
                  <label class="label-control col-md-3">{{__('header.Reject Reason')}}:</label>
                  <div class="col-md-8">
                    <select class="form-control" name="reasonType" id="reasonType">
                      <option value="1">Invalid Ngo</option>
                      <option value="2">Invalid details</option>
                      <option value="3">Other</option>
                    </select>
                  </div>
                </div>
                <div class="form-group clearfix">
                  <label class="label-control col-md-3">{{__('header.Remark')}}:</label>
                  <div class="col-md-8">
                    <input type="text" class="form-control" name="rejectText" id="rejectText" placeholder="{{__('header.Enter Reason')}}" value=""/>
                  </div>
                </div>
                <div class="form-group clearfix">
                  <div class="col-md-12">
                    <input id="notify" class="btn btn-info btn-block" type="submit" value="{{__('header.Submit')}}">
                  </div>
                </div>
              </div>
            </div>
            <span class="alert-danger text-center" id="errorReason" style="display: block;"></span>
          </form>
        </div>
      </div>
    </div>
  </div>

</div>

{{$ngo->appends(Request::except('page'))->links()}}
@stop
@section('footer_scripts')
  <script type="text/javascript">
    $(document).ready(function()
    {
      $(".Reject").click(function()
      {
        var id = $(this).attr('data');
        $('#rejectModal #id').val(id);
        document.getElementById('errorReason').innerHTML = '';
        $('#rejectModal').modal('show');
      });

      $(document).on('submit', '#rejectForm', function(e)
      {
        e.preventDefault();
        var text = $("#rejectText").val();
        if(text != "")
        {
          var rejectType = $("#reasonType").val();
          var id = $('#rejectModal #id').val();
          window.location.href = '{{URL::to('admin/statusrejectNgo')}}/'+id+'/'+text+'/'+rejectType;
        }
        else
        {
          var rejectType = $("#reasonType").val();
          var id = $('#rejectModal #id').val();
          window.location.href = '{{URL::to('admin/statusreject')}}/'+id+'/empty/'+rejectType;
        }
      });
    });

    $(document).ready(function()
    {
      $.DrLazyload(); //Yes! that's it!
    });
  </script>
@stop