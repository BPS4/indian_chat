<script>
    var addedTest = '';
</script>
<div class="form-group col-md-12">
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalLong">
        Click here to add test
    </button>
</div>
<!-- The Modal -->
<div class="modal fade centre-timing" id="exampleModalLong" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-centered  modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="search-text">
                    <h4 class="modal-title">Select Test</h4>
                    <label> Search</label>:
                    <input type="search" class="form-control form-control-sm" id="searchTest" autocomplete="off">
                </div>
                <button type="button" class="close fa-2x" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <?php
            $addedTestsArr = [];
            $activeTests = getActiveTests(50);
            if (isset($details->tests) && !empty($details->tests)) {
                $addedTests = json_decode($details->tests, 1);
            } else {
                $addedTests = '';
            }
            if ($addedTests) {
                foreach ($addedTests as $testKey => $test) {
                    $addedTestsArr[] = $test['test_id'];
                }
            }
            ?>
            <div class="modal-body">

                <div class="row col-md-12 addedActiveTests">
                    <div class="row col-md-12">
                        <label class="form-check-label">
                            <strong>Added Tests</strong>
                        </label>
                    </div>
                    @if( $addedTests)

                    @foreach( $addedTests as $aKey => $addedTest)
                    <div class="col-md-4 add_test_{{$addedTest['test_id']}} pl-0 " text-name="{{$addedTest['test_name']}}">
                        <label class="remove-test" onclick="removeTest('{{$addedTest['test_id']}}')">&times;</label>
                        <div class="add-test-border  p-2 ">
                            <input type="hidden" name="selected_test_name[{{$addedTest['test_id']}}]" value="{{$addedTest['test_name']}}">
                            <input type="hidden" name="selected_test_id[]" class="form-check-input" value="{{$addedTest['test_id']}}">
                            <label class="form-check-label">{{$addedTest['test_name']}}</label>
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>
                <div class="row col-md-12">
                    <hr class="w-100">
                    <label class="form-check-label">
                        <strong> Search Results</strong>
                    </label>
                </div>
                <div class="row col-md-12 activeTests">
                    @if($activeTests)
                    @foreach($activeTests as $tKey => $tList)
                    @if(!in_array($tList->id, $addedTestsArr))
                    <div class="col-md-3 col_test_row" text-name="{{$tList->test_name}}">
                        <input type="checkbox" name="" class="form-check-input" id="_test_{{$tList->id}}" component='' value="{{$tList->id}}" testname="{{$tList->test_name}}" onchange="addRemoveTest(this)">
                        <label class="form-check-label" for="_test_{{$tList->id}}">{{$tList->test_name}}</label>
                    </div>
                    @endif
                    @endforeach
                    @endif
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary font-weight-bold">Save changes</button>
            </div>

        </div>
    </div>
</div>