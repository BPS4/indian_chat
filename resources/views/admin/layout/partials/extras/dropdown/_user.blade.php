 {{-- Header --}}
 <div class="d-flex align-items-center justify-content-between flex-wrap p-8 bgi-size-cover bgi-no-repeat rounded-top" style="background-image: url('{{ asset('media/misc/bg-1.jpg') }}')">
     <div class="d-flex align-items-center mr-2">
         {{-- Symbol --}}
         <div class="symbol bg-white-o-15 mr-3">
             <span class="symbol-label text-success font-weight-bold font-size-h4">A</span>
         </div>

         {{-- Text --}}
         <div class="text-white m-0 flex-grow-1 mr-3 font-size-h5">Admin</div>
     </div>
 </div>

 {{-- Nav --}}
 <div class="navi navi-spacer-x-0 pt-5">
     {{-- Item --}}
     <a href="#" class="navi-item px-8">
         <div class="navi-link">
             <div class="navi-icon mr-2"> 
                 <i class="flaticon2-calendar-3 text-success"></i>
             </div>
             <div class="navi-text">
                 <div class="font-weight-bold">
                     My Profile
                 </div>
                 <div class="text-muted">
                     Account settings and more
                     <span class="label label-light-danger label-inline font-weight-bold">update</span>
                 </div>
             </div>
         </div>
     </a>


     {{-- Footer --}}
     <div class="navi-separator mt-3"></div>
     <div class="navi-footer  px-8 py-5">
         <a href="{{url('logout')}}" class="btn btn-light-primary font-weight-bold">Sign Out</a> 
     </div>
 </div>