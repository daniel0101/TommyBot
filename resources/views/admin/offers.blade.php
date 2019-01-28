@extends('layouts.admin')
@section('content')
<div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
          <h4 class="card-title">Offers <a href="{{ url('/admin/offer') }}" class="btn btn-info btn-round btn-sm">New Offer</a></h4>
          </div>
          <div class="card-body">
              <table class="table">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>URL</th>
                        <th>Offer Date</th>
                        <th>Status</th>
                        <th class="">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($offers as $i=>$offer)
                    <tr>
                        <td class="text-center">{{ $i+1 }}</td>
                        <td>{{ $offer->name }}</td>
                        <td>{{ $offer->description }}}</td>
                        <td>{{ $offer->url }}</td>
                        <td>{{ $offer->offer_date }}</td>
                        <td><span class="badge badge-{{ $offer->offer_date > date('Y-m-d')?'info':'danger' }}">{{ $offer->offer_date > date('Y-m-d')?'Ongoing':'Expired' }}</span></td>
                        <td class="td-actions">
                            <a href="{{ url('/admin/offer/'.$offer->id) }}" data-toggle="tooltip" title="Edit Offer" class="btn btn-info btn-sm btn-icon btn-round">
                                <i class="tim-icons icon-pencil"></i>
                            </a>
                            <a href="{{ url('/admin/delete/'.$offer->id) }}" data-toggle="tooltip" title="Delete Offer" class="btn btn-danger btn-sm btn-icon btn-round" onclick="return confirm('Are you sure?');">
                                <i class="tim-icons icon-simple-remove"></i>
                            </a>
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
@endsection