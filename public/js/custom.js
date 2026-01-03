var mobileRegix = /^\d{10}$/;
const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;



$(document).ready(function () {
    console.log('PAGE LOADED');
    $("form").submit(function (event) {
        $('.error-class').remove();
        var flag = false;
        var firstInput = null;
        var inputs = $(this).find('input');
        var selectInputs = $(this).find('select');
        var textareaInputs = $(this).find('textarea');
        Array.prototype.push.apply(inputs, selectInputs);
        Array.prototype.push.apply(inputs, textareaInputs);
        $.each(inputs, function (k, v) {
            var hasAttr = $(this).attr('isrequired');
            var val = $(this).val();
            if ($(this).attr('name'))
                var NameAttr = $(this).attr('name').toLowerCase();
            else
                var NameAttr = '';

            if (hasAttr) {
                if (!val) {
                    if (!firstInput)
                        firstInput = this;
                    $(this).after('<div class="error-class">This field is required.</div>');
                    flag = true;
                }
                if (NameAttr == 'mobile' || NameAttr == 'phone' || NameAttr == 'mobile_no' || NameAttr == 'head_mobile') {
                    if (!val.match(mobileRegix) && val != '') {
                        if (!firstInput)
                            firstInput = this;
                        if ($(this).nextAll().length > 0) {
                            $(this).nextAll().after('<div class="error-class">Enter valid mobile.</div>');
                        } else {
                            $(this).after('<div class="error-class">Enter valid mobile.</div>');
                        }
                        flag = true;
                    }
                }
                if (NameAttr == 'email_id' || NameAttr == 'email') {
                    if (!validateEmail($(this).val()) && val != '') {
                        if (!firstInput)
                            firstInput = this;
                        if ($(this).nextAll().length > 0) {
                            $(this).nextAll().after('<div class="error-class">Enter valid email.</div>');
                        } else {
                            $(this).after('<div class="error-class">Enter valid email.</div>');
                        }
                        flag = true;

                    }
                }
                if (NameAttr == 'gst' || NameAttr == 'gst_number') {
                    if (val != '') {
                        if (val.length != 15) {
                            var subString = val.substring(2);
                            var panNo = subString.substring(0, 10);
                            if (!validatePAN(null, panNo)) {
                                if (!firstInput)
                                    firstInput = this;
                                if ($(this).nextAll().length > 0) {
                                    $(this).nextAll().after('<div class="error-class">Enter valid GST number.</div>');
                                } else {
                                    $(this).after('<div class="error-class">Enter valid GST number.</div>');
                                }
                                flag = true;

                            }
                        }
                    }
                }
                if (NameAttr == 'dob') {
                    var maxVal = $(this).attr('max');
                    if (!maxVal)
                        var maxVal = new Date();
                    if (!moment(new Date()).isAfter(val)) {

                        if ($(this).nextAll().length > 0) {
                            $(this).nextAll().after('<div class="error-class">The DOB Date must be Lesser or Equal to today date.</div>');
                        } else {
                            $(this).after('<div class="error-class">The DOB Date must be Lesser or Equal to today date.</div>');
                        }
                        if (!firstInput)
                            firstInput = this;

                        flag = true;

                    }
                }
                if (NameAttr == 'selling_price') {
                    var mrp = parseFloat($('input[name=mrp]').val());
                    var sellingPrice = parseFloat(val);

                    if (mrp < sellingPrice) {

                        if ($(this).nextAll().length > 0) {
                            $(this).nextAll().after('<div class="error-class">Selling price not greater then MRP.</div>');
                        } else {
                            $(this).after('<div class="error-class">Selling price not greater then MRP.</div>');
                        }
                        if (!firstInput)
                            firstInput = this;

                        flag = true;

                    }
                }
                // if (NameAttr == 'otp') {
                //     if ($(this).val().length < 6 && val != '') {
                //         if (!firstInput)
                //             firstInput = this;
                //         if ($(this).nextAll().length > 0) {
                //             $(this).nextAll().after('<div class="error-class">Enter 6 digit OTP.</div>');
                //         } else {
                //             $(this).after('<div class="error-class">Enter 6 digit OTP.</div>');
                //         }
                //         flag = true;
                //         //  $(this).focus();
                //     }
                // }

            } else {
                if (NameAttr == 'selling_price' && val != '') {
                    var mrp = parseFloat($('input[name=mrp]').val());
                    var sellingPrice = parseFloat(val);

                    if (mrp < sellingPrice) {

                        if ($(this).nextAll().length > 0) {
                            $(this).nextAll().after('<div class="error-class">Selling price not greater then MRP.</div>');
                        } else {
                            $(this).after('<div class="error-class">Selling price not greater then MRP.</div>');
                        }
                        if (!firstInput)
                            firstInput = this;

                        flag = true;

                    }
                }
            }

        });
        if (flag) {

            if (firstInput) {
                $(firstInput).focus();
            }
            return false;
        } else {

            return true;
        }
        event.preventDefault();
    });

    $('#country_id').change(function () {
        console.log('IN country');
        var str = $(this).val();
        var stateName = $(this).find('option[value=' + str + ']').attr('data-name');
        if (typeof stateId !== 'undefined') {
            var state_id = stateId;
        }
        console.log(str);
        if (str.length > 0) {
            $.ajax({
                type: 'GET',
                url: APP_URL + '/ajax/states/' + str,
                dataType: 'json',

                success: function (rensponse) {
                    var option = '<option value="">Select State</option>';
                    $.each(rensponse, function (index, value) {
                        var selected = '';
                        if (typeof state_id !== 'undefined') {
                            if (state_id == value.id) {
                                var selected = 'selected';
                            }
                        }
                        option += '<option value="' + value.id + '" ' + selected + '> ' + value.name + ' </option>';
                    })
                    $('#state').html(option);
                }
            })
        }
    });
    $('#state').change(function () {
        console.log('IN state');
        var str = $(this).val();
        var stateName = $(this).find('option[value=' + str + ']').attr('data-name');
        if (typeof cityId !== 'undefined') {
            var city_id = cityId;
        }
        console.log(str);
        if (str.length > 0) {
            $.ajax({
                type: 'GET',
                url: APP_URL + '/ajax/cities/' + str,
                dataType: 'json',

                success: function (rensponse) {
                    var option = '<option value="">Select City</option>';
                    $.each(rensponse, function (index, value) {
                        var selected = '';
                        if (typeof city_id !== 'undefined') {
                            if (city_id == value.id) {
                                var selected = 'selected';
                            }
                        }
                        option += '<option value="' + value.id + '" ' + selected + '> ' + value.name + ' </option>';
                    })
                    $('#city').html(option);
                }
            })
        }
    });
    $('#category').change(function () {
        var str = $(this).val();
        console.log(str);
        if (str) {
            var categoryId = str.toString();
            $.ajax({
                type: 'GET',
                url: APP_URL + '/ajax/subcategories?categoryId=' + categoryId,
                dataType: 'json',

                success: function (rensponse) {
                    var option = '<option value="">Select</option>';
                    $.each(rensponse, function (index, value) {
                        var selected = '';
                        if (typeof subcategoryId !== 'undefined') {
                            if (subcategoryId == value.id) {
                                var selected = 'selected';
                            }
                        }
                        option += '<option value="' + value.id + '" ' + selected + '> ' + value.category_name + ' </option>';
                    })
                    $('#subcategory').html(option);
                    $('#subcategory').multiselect('rebuild');
                    $('#subcategory').multiselect({
                        nonSelectedText: 'Select Sub Category',
                        includeSelectAllOption: true,
                        enableFiltering: true,
                        enableCaseInsensitiveFiltering: true,
                        buttonWidth: '100%'
                    });
                }
            })
        }
    });
    $('#department').change(function () {
        var str = $(this).val();
        console.log('str');
        console.log(str);
        if (str) {
            getSpecialities(str);
        }
    });
    $("#searchTest").on("keyup", function () {
        var value = $(this).val().toLowerCase();
        $(".col_test_row").filter(function () {
            $(this).toggle($(this).attr('text-name').toLowerCase().indexOf(value) > -1)
        });
    });

    $('#clinic_day').click(function () {
        $('#Day').fadeIn();
        $('#Week').fadeOut();
    });
    $('#clinic_week').click(function () {
        $('#Day').fadeOut();
        $('#Week').fadeIn();
    });

    $('.addRowBtn').click(function () {
        var thisId = $(this).attr('row-id');
        $("." + thisId + " .row:first").clone().insertAfter("." + thisId + " .row:last").find('input').val('');
    });

    setRequiredLavelAlert();
});
$('input.number').on('input', function () {
    this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1');
});

function changeStatus(e) {
    if (confirm("Do you want to change status?")) {
        var url = $(e).attr('data-url');
        if (url)
            location.href = url;
    }
}

function deleteItem(e) {
    if (confirm("Are sure want to delete?")) {
        var url = $(e).attr('data-url');
        if (url)
            location.href = url;
    }
}

function updateImage(e) {
    if (e) {
        $(e).parents('.form-group').find('._update_img_file').toggle();
        $(e).parent('._update_img_action').toggle();
    } else {
        $('._update_img_file').toggle();
        $('._update_img_action').toggle();
    }
}

function getSpecialities(departmentIds) {
    var str = departmentIds;
    if (str) {
        var department_id = str.toString();
        $.ajax({
            type: 'GET',
            url: APP_URL + '/ajax/specilities?departmentId=' + department_id,
            dataType: 'json',

            success: function (rensponse) {
                var option = '';
                $.each(rensponse, function (index, value) {
                    var selected = '';
                    if (typeof addedSpecialities !== 'undefined') {
                        if (addedSpecialities) {
                            addedSpecialitiesArr = JSON.parse(addedSpecialities);

                            if ($.inArray(value.id.toString(), addedSpecialitiesArr) != '-1' || addedSpecialitiesArr.includes(value.id)) {
                                selected = 'selected';
                            } else {
                                selected = '';
                            }
                        }
                    }
                    option += '<option value="' + value.id + '" ' + selected + '> ' + value.speciality_name + ' </option>';
                })
                $('#speciality').html(option);
                $('#speciality').multiselect('rebuild');
                $('#speciality').multiselect({
                    nonSelectedText: 'Select Sub Category',
                    includeSelectAllOption: true,
                    enableFiltering: true,
                    enableCaseInsensitiveFiltering: true,
                    buttonWidth: '100%'
                });
            }
        })
    }
}

function getSubCategories(categoryIds) {
    var str = categoryIds;
    if (str) {
        var categoryId = str.toString();
        $.ajax({
            type: 'GET',
            url: APP_URL + '/ajax/subcategories?categoryId=' + categoryId,
            dataType: 'json',

            success: function (rensponse) {
                var option = '';
                $.each(rensponse, function (index, value) {
                    var selected = '';

                    if (typeof addedSubCategories !== 'undefined') {
                        if (addedSubCategories) {
                            addedSubCategoriesArr = JSON.parse(addedSubCategories);
                            // console.log(addedSubCategoriesArr)
                            if ($.inArray(value.id.toString(), addedSubCategoriesArr) != '-1' || addedSubCategoriesArr.includes(value.id)) {
                                selected = 'selected';
                            } else {
                                selected = '';
                            }

                        }
                    }
                    option += '<option value="' + value.id + '" ' + selected + '> ' + value.category_name + ' </option>';
                })
                $('#subcategory').html(option);
                $('#subcategory').multiselect('rebuild');
                $('#subcategory').multiselect({
                    nonSelectedText: 'Select Sub Category',
                    includeSelectAllOption: true,
                    enableFiltering: true,
                    enableCaseInsensitiveFiltering: true,
                    buttonWidth: '100%'
                });
            }
        })
    }
}

function fileValidation(e) {
    $('.error-class').remove();
    var fileInput = e;
    var filePath = fileInput.value;
    // Allowing file type
    var allowedExtensions =
        /(\.mp4|\.wmv)$/i;

    if (!allowedExtensions.exec(filePath)) {
        $(e).after('<div class="error-class">Invalid file type .mp4, .wmv are allowed.</div>');
        fileInput.value = '';
        return false;
    }
}

function clinic_day() {
    document.getElementById("Day").style.display = "block";
    document.getElementById("Week").style.display = "none";
}

function clinic_week() {
    document.getElementById("Week").style.display = "block";
    document.getElementById("Day").style.display = "none";
}

function removeIt(e) {
    var thisId = $(e).attr('row-id');
    var childId = $(e).attr('row-child-id');
    if ($("." + thisId + " .row").length == 1) {
        $("." + thisId + " .row:first").find('input').val('');
    } else {
        $(e).parents('.child_row').remove();
    }
}

function setRequiredLavelAlert() {
    var inputs = $('form').find('input');
    var selectInputs = $('form').find('select');
    var textareaInputs = $('form').find('textarea');

    Array.prototype.push.apply(inputs, selectInputs);
    Array.prototype.push.apply(inputs, textareaInputs);
    $.each(inputs, function (k, v) {
        var hasAttr = $(this).attr('isrequired');
        var current = this;
        if (hasAttr) {
            $(current).parents('.form-group').find('label').first().append('<small class="color-red"><strong>*</strong></small>');
            if ($(this).attr('name'))
                var NameAttr = $(this).attr('name').toLowerCase();
            else
                var NameAttr = '';
            if (NameAttr == 'mobile' || NameAttr == 'phone' || NameAttr == 'mobile_no') {
                $(this).attr('maxlength', 10);
            }
        }
    });
}

function validateEmail(email) {
    return re.test(String(email).toLowerCase());
}

function validatePAN(gstIn = null, PanVal) {
    if (gstIn) {
        var subString = gstIn.substring(2);
        var panNo = subString.substring(0, 10);
        if (PanVal == panNo) {
            return true;
        } else {
            return false;
        }
    } else {
        if (PanVal) {
            var panVal = PanVal;
            var regpan = /^([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}?$/;
            if (regpan.test(panVal)) {
                // valid pan card number
                console.log(true);
                return true;
            } else {
                console.log(false);
                // invalid pan card number
                return false;
            }
        }
        return false;
    }
}

function generateSlug(e, id = null) {
    string = $(e).val().replace(/[^a-zA-Z0-9-/]/g, '-');
    string = string.replace('--', '-');
    lastChar = string.substr(string.length - 1);
    if (!id)
        $(e).val(string.toLowerCase());
    else
        $(id).val(string.toLowerCase());
}

function removeTest(id) {
    $('.add_test_' + id).remove();
}

function addRemoveTest(e) {
    var id = $(e).val();
    var name = $(e).attr('testname');
    if ($('.add_test_' + id).length > 0) {
        removeTest(id);
    } else {
        var htm = `
    <div class="col-md-4 add_test_` + id + ` pl-0 mt-2" text-name="` + name + `">
                        <label class="remove-test" onclick="removeTest('` + id + ` ')">&times;</label>
                        <div class="add-test-border  p-2 ">
                            <input type="hidden" name="selected_test_name[` + id + `]" value="` + name + `">
                            <input type="hidden" name="selected_test_id[]" class="form-check-input" value="` + id + `">
                            <label class="form-check-label">` + name + `</label>
                        </div>
                    </div>
                    `;
        console.log(htm);
        $('.addedActiveTests').append(htm);
    }

}

$("#accordion").on("hide.bs.collapse show.bs.collapse", e => {
    $(e.target)
      .prev()
      .find("i:last-child")
      .toggleClass("fa-minus fa-plus");
  });
  