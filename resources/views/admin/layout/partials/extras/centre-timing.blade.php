<!-- Button trigger modal-->
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalLong">
    Manage Centre Timings
</button>

<!-- Modal-->
<div class="modal fade centre-timing" id="exampleModalLong" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Clinic Timings</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-lg-2">
                        <div class="clinic-card text-end d-none">
                            <input type="radio" id="clinic_day" name="radio-group" value="" checked />
                            <label for="clinic_day">Day</label>
                        </div>
                    </div>
                    <!-- <div class="col-lg-6">
                        <input type="radio" id="clinic_week" name="radio-group" value="2" />
                        <label for="clinic_week">Full Week</label>
                    </div> -->
                </div>

                @php
                $weekDaysArray = weekDaysArray();
                if (isset($details->centre_timings) && !empty($details->centre_timings)) {
                $addedTimings = json_decode($details->centre_timings, true);
                }else{
                $addedTimings = [];
                }
                @endphp

                <div class="Day" id="Day">


                    @if($weekDaysArray)
                    @foreach($weekDaysArray as $key => $list)
                    <div class="">
                        <div class="col-lg-3 pl-0">
                            <?php
                            if (isset($addedTimings[$key])) {
                                if ($addedTimings[$key]['is_active'] == 1) { ?>
                                    <input type="checkbox" name="day[{{$key}}]" value="{{$list}}" id="_d_{{$key}}" checked />
                                <?php   } else { ?>
                                    <input type="checkbox" name="day[{{$key}}]" value="{{$list}}" id="_d_{{$key}}" />
                                <?php } ?>
                            <?php } else { ?>
                                <input type="checkbox" name="day[{{$key}}]" value="{{$list}}" id="_d_{{$key}}" checked />
                            <?php } ?>

                            <label for="_d_{{$key}}">{{$list}}</label>
                        </div>

                        <div class="row col-md-12 pl-0 pr-0">
                            <div class="form-group col-md-4">
                                <label><strong>Open</strong></label>
                            </div>
                            <div class="form-group col-md-4">
                                <label><strong>Close</strong></label>
                            </div>
                        </div>
                        <span class="addMoreRow_{{$key}}">
                            <?php $timings = [];
                            if (isset($addedTimings[$key]) && count($addedTimings[$key]['timings']) > 0) {
                                foreach ($addedTimings[$key]['timings'] as $ocKey => $ocList) { ?>
                                    <div class="row col-md-12 pl-0 pr-0 child_row">
                                        <div class="form-group col-md-4">
                                            <div>
                                                <input type="time" name="open[{{$key}}][]" value="<?php if ($ocList['open']) {
                                                                                                        echo $ocList['open'];
                                                                                                    } else {
                                                                                                        if ($key < 6) {
                                                                                                            echo '08:00';
                                                                                                        } else {
                                                                                                            echo '08:00';
                                                                                                        }
                                                                                                    } ?>" class="form-control" />
                                            </div>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <div>
                                                <input type="time" name="close[{{$key}}][]" value="<?php if ($ocList['close']) {
                                                                                                        echo $ocList['close'];
                                                                                                    } else {
                                                                                                        if ($key < 6) {
                                                                                                            echo '20:00';
                                                                                                        } else {
                                                                                                            echo '14:00';
                                                                                                        }
                                                                                                    } ?>" class="form-control" />
                                            </div>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <div class="btn btn-danger removeRowBtn" row-id="addMoreRow_{{$key}}" onclick="removeIt(this)"><i class="fa fa-trash"></i></div>

                                        </div>
                                    </div>
                                <?php     }
                            } else { ?>
                                <div class="row col-md-12 pl-0 pr-0 child_row">
                                    <div class="form-group col-md-4">
                                        <div>
                                            <input type="time" name="open[{{$key}}][]" class="form-control" value="@if($key < 6){{'08:00'}}@else{{'08:00'}}@endif" />
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <div>
                                            <input type="time" name="close[{{$key}}][]" class="form-control" value="@if($key < 6){{'20:00'}}@else{{'14:00'}}@endif" />
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <div class="btn btn-danger removeRowBtn" row-id="addMoreRow_{{$key}}" onclick="removeIt(this)"><i class="fa fa-trash"></i></div>

                                    </div>
                                </div>
                            <?php }
                            ?>

                        </span>
                        <div class="form-group col-md-9 text-right pr-0">
                            <div class="btn btn-primary addRowBtn" row-id="addMoreRow_{{$key}}"><i class="fa fa-plus"></i></div>
                        </div>

                        <div class="form-group col-md-12">
                            <hr>
                        </div>
                    </div>
                    @endforeach
                    @endif

                </div>
















                <!-- <div class="Week" id="Week" style="display: none">
                    <div class="multi-field-wrapper ">
                        <div class="multi-fields">
                            <div class="multi-field row">
                                <div class="form-group col-md-1">
                                    <label>Open</label>
                                    <div>
                                        <input type="time" name="open" class="form-control" />
                                    </div>
                                </div>
                                <div class="form-group col-md-1">
                                    <label>Close</label>
                                    <div>
                                        <input type="time" name="close" class="form-control" />
                                    </div>
                                </div>
                                <div class="
                      btn btn-danger 
                      close-btn
                      remove-field
                      text-right
                    ">
                                    -
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-2 ">
                            <div class="btn btn-primary weekRow" id="weekRow">+</div>
                        </div>
                    </div>
                </div> -->


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" id="closeCTModel" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary font-weight-bold" onclick="$('#closeCTModel').click()">Save changes</button>
            </div>
        </div>
    </div>
</div>
@section('styles')

<style>
    .btn i {
        color: #FFFFFF;
        font-size: 13px;
        padding: 0;
    }

    .centre-timing .form-group {
        margin-bottom: 10px;
    }
</style>
@endsection