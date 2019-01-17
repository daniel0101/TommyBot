@extends('layouts.admin')
@section('content')
<div class="content">
        <div class="row">
          <div class="col-md-8 ml-auto mr-auto">
            <div class="card">
                <div class="card-header">
                    <h3 class="title">Offer</h3>
                </div>
                <form method="POST" action="{{ isset($offer)?url('/admin/offer/'.$offer->id):url('/admin/offer')}}" enctype="multipart/form-data">
                @csrf
                <div class="card-body">                
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Name</label>
                            <input type="text" name="name" class="form-control"  placeholder="Offer Name" value="{{ isset($offer)? $offer->name: '' }}" required>
                            </div>
                        </div>                    
                    </div>
                    <div class="row">
                        <div class="col-md-{{ isset($offer)?'6':'12'}} pr-md-1">
                            <div class="form-group">
                                <label>Image</label>
                                <input type="file" name="img" class="form-control" placeholder="" value="">
                            </div>
                        </div>
                        @if(isset($offer))
                        <div class="col-md-6 pl-md-1">
                            <img src="{{ $offer->image }}" class="img-thumbnail" alt="">
                        </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Url (Where users visit to view offer)</label>
                                <input type="text" name="url" class="form-control" placeholder="Url" value="{{ isset($offer)?$offer->url:'' }}">
                            </div>
                        </div>
                    </div> 
                    <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Offer Date</label>
                                    <input type="date" class="form-control" name="offer_date" placeholder="Offer Date" value="{{ isset($offer)?date('Y-m-d', strtotime($offer->offer_date)):'' }}">
                                </div>
                            </div>
                        </div>                 
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Description</label>
                                <textarea rows="4" cols="80" class="form-control" name="description" placeholder="Here can be your description" value="{{ isset($offer)?$offer->description:'' }}">{{ isset($offer)?$offer->description:'' }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-fill btn-primary btn-block">Save</button>
                </div>
                </form>
            </div>
          </div>
          {{-- <div class="col-md-4">
            <div class="card card-user">
              <div class="card-body">
                <p class="card-text">
                  <div class="author">
                    <div class="block block-one"></div>
                    <div class="block block-two"></div>
                    <div class="block block-three"></div>
                    <div class="block block-four"></div>
                    <a href="javascript:void(0)">
                      <img class="avatar" src="../assets/img/emilyz.jpg" alt="...">
                      <h5 class="title">Mike Andrew</h5>
                    </a>
                    <p class="description">
                      Ceo/Co-Founder
                    </p>
                  </div>
                </p>
                <div class="card-description">
                  Do not be scared of the truth because we need to restart the human foundation in truth And I love you like Kanye loves Kanye I love Rick Owens’ bed design but the back is...
                </div>
              </div>
              <div class="card-footer">
                <div class="button-container">
                  <button href="javascript:void(0)" class="btn btn-icon btn-round btn-facebook">
                    <i class="fab fa-facebook"></i>
                  </button>
                  <button href="javascript:void(0)" class="btn btn-icon btn-round btn-twitter">
                    <i class="fab fa-twitter"></i>
                  </button>
                  <button href="javascript:void(0)" class="btn btn-icon btn-round btn-google">
                    <i class="fab fa-google-plus"></i>
                  </button>
                </div>
              </div>
            </div>
          </div> --}}
        </div>
    </div>    
@endsection