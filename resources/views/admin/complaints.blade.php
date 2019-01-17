@extends('layouts.admin')
@section('content')
<div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
          <h4 class="card-title">Complaints</h4>
          </div>
          <div class="card-body">
              <table class="table">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th class="">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($complaints as $i=>$complaint)
                    <tr>
                        <td class="text-center">{{ $i+1 }}</td>
                        <td>{{ $complaint->firstname }}</td>
                        <td>{{ $complaint->email }}}</td>
                        <td>{{ $complaint->message }}</td>
                        <td><span class="badge badge-{{ $complaint->reply_status?'success':'danger' }}">{{ $complaint->reply_status?'replied':'new' }}</span></td>
                        <td class="td-actions">
                            <button type="button" data-id="{{ $complaint->id }}" data-email="{{ $complaint->email }}" rel="tooltip" data-toggle="modal" data-target="#reply" title="Send Reply Email" class="btn btn-info btn-sm btn-icon btn-round reply">
                                <i class="tim-icons icon-email-85"></i>
                            </button>                           
                        </td>
                    </tr>
                    @endforeach                    
                </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
</div>

<div id="reply" class="modal modal-dark" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Complaint Reply</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="/admin/reply" method="POST">
            @csrf
        <div class="modal-body">
        <input type="hidden" name="id" id="complaint_id">
            <div class="form-group">
                <input type="email" name="email" id="email" placeholder="Email Address" class="form-control" required style="color: black">
            </div>
            <div class="form-group">
                <input type="text" name="subject" placeholder="Subject" class="form-control" required style="color: black">
            </div>
            <div class="form-group">
                <textarea name="message" id="editor" class="form-control" cols="30" rows="10" placeholder="Type your reply here..." required style="color: black">                    
                </textarea>
            </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-info btn-round" style="width: 100%">Send Reply</button>
          {{-- <button type="button" class="btn btn-secondary btn-round" data-dismiss="modal">Close</button> --}}
        </div>
        </form>
      </div>
    </div>
  </div>
  <script src="https://cdn.ckeditor.com/ckeditor5/11.2.0/classic/ckeditor.js"></script>
  <script>
      $(document).ready(()=>{
        ClassicEditor
            .create( document.querySelector( '#editor' ) )
            .then( editor => {
                console.log( editor );
            } )
            .catch( error => {
                console.error( error );
            } );

          $('.reply').click(function(){
            // console.log($(this).attr('type'));
            $('#email').val($(this).data('email'));
            $('#complaint_id').val($(this).data('id'));
          });
      });
  </script>
@endsection