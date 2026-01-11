@extends('admin.layout.default')
@section('hotels', 'active menu-item-open')

@section('content')
    <div class="card card-custom">
        <div class="card-body">
            <div class="mb-7">
                <div class="booking-wrapper">
                

                        <div class="booking-card">
                            <h5 class="title">Create New Message</h5>
                            <p class="subtitle">Follow the steps to create a new Message</p>


                            <!-- STEP 1: Basic Details -->
                    <form action="{{ route('message.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    {{-- GLOBAL ERROR MESSAGE --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            Please fix the errors below.
        </div>
    @endif

    <div class="step-content" id="step1">
        <p class="fw-bold">Create New Message</p>
        <br>

        <div class="row g-3">

            {{-- Media --}}
            <div class="form-group col-md-6">
                <label class="fw-bold">Select Image / Video</label>
                <input type="file" name="media"
                       class="form-control @error('media') is-invalid @enderror"
                       accept="image/*,video/*">
                @error('media')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- YouTube --}}
            <div class="form-group col-md-6">
                <label class="fw-bold">YouTube Link</label>
                <input type="url" name="youtube_link"
                       class="form-control @error('youtube_link') is-invalid @enderror"
                       value="{{ old('youtube_link') }}">
                @error('youtube_link')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Description --}}
            <div class="form-group col-md-12">
                <label class="fw-bold">Message Description</label>
                <textarea name="description" rows="5"
                          class="form-control @error('description') is-invalid @enderror"
                          required>{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Calling Number --}}
            <div class="form-group col-md-6">
                <label class="fw-bold">Calling Number</label>
                <input type="tel" name="calling_number"
                       class="form-control @error('calling_number') is-invalid @enderror"
                       value="{{ old('calling_number') }}" required>
                @error('calling_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Website --}}
            <div class="form-group col-md-6">
                <label class="fw-bold">Website Link</label>
                <input type="url" name="website_link"
                       class="form-control @error('website_link') is-invalid @enderror"
                       value="{{ old('website_link') }}">
                @error('website_link')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Instagram --}}
            <div class="form-group col-md-6">
                <label class="fw-bold">Instagram</label>
                <input type="url" name="instagram_link"
                       class="form-control @error('instagram_link') is-invalid @enderror"
                       value="{{ old('instagram_link') }}">
                @error('instagram_link')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Facebook --}}
            <div class="form-group col-md-6">
                <label class="fw-bold">Facebook</label>
                <input type="url" name="facebook_link"
                       class="form-control @error('facebook_link') is-invalid @enderror"
                       value="{{ old('facebook_link') }}">
                @error('facebook_link')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Telegram --}}
            <div class="form-group col-md-6">
                <label class="fw-bold">Telegram</label>
                <input type="url" name="telegram_link"
                       class="form-control @error('telegram_link') is-invalid @enderror"
                       value="{{ old('telegram_link') }}">
                @error('telegram_link')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Country --}}
            <div class="form-group col-md-6">
                <label class="fw-bold">Country</label>
                <input type="text" name="country"
                       class="form-control"
                       value="India" readonly>
            </div>

            {{-- State --}}
            <div class="form-group col-md-6">
                <label class="fw-bold">State</label>
                <select name="state"
                        class="form-control @error('state') is-invalid @enderror"
                        required>
                    <option value="">Select State</option>
                    @php
                        $states = [
                            'Andhra Pradesh','Arunachal Pradesh','Assam','Bihar','Chhattisgarh',
                            'Goa','Gujarat','Haryana','Himachal Pradesh','Jharkhand',
                            'Karnataka','Kerala','Madhya Pradesh','Maharashtra','Manipur',
                            'Meghalaya','Mizoram','Nagaland','Odisha','Punjab',
                            'Rajasthan','Sikkim','Tamil Nadu','Telangana','Tripura',
                            'Uttar Pradesh','Uttarakhand','West Bengal',
                            'Andaman and Nicobar Islands','Chandigarh',
                            'Dadra and Nagar Haveli and Daman and Diu',
                            'Delhi','Jammu and Kashmir','Ladakh',
                            'Lakshadweep','Puducherry'
                        ];
                    @endphp
                    @foreach($states as $state)
                        <option value="{{ $state }}" {{ old('state') == $state ? 'selected' : '' }}>
                            {{ $state }}
                        </option>
                    @endforeach
                </select>
                @error('state')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- City --}}
            <div class="form-group col-md-6">
                <label class="fw-bold">City</label>
                <input type="text" name="city"
                       class="form-control @error('city') is-invalid @enderror"
                       value="{{ old('city') }}">
                @error('city')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Auto Send --}}
            <div class="form-group col-md-6 mt-2">
                <div class="form-check">
                    <input type="checkbox" name="auto_send"
                           class="form-check-input"
                           value="1" {{ old('auto_send') ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold">
                        Automatically send this message
                    </label>
                </div>
            </div>

            {{-- Total Users --}}
            <div class="form-group col-md-6 mt-2">
                <label class="fw-bold">Total Users</label>
                <input type="number" name="total_users"
                       class="form-control @error('total_users') is-invalid @enderror"
                       value="{{ old('total_users') }}">
                @error('total_users')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

        </div>

        {{-- Buttons --}}
        <div class="text-end mt-4">
            <button type="reset" class="btn btn-outline-secondary">Cancel</button>
            <button type="submit" class="btn btn-success">Send Message</button>
        </div>

    </div>
</form>








                        </div>
                    </form>

                </div>


            </div>
        </div>
        </form>
    </div>
    </div>
    </div>
    </div>
@endsection
{{-- Styles Section --}}


@section('scripts')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.25.1/standard/ckeditor.js"></script>
    <script src="https://cdn.ckeditor.com/4.25.1/standard/ckeditor.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll('.ckeditor').forEach(textarea => {
                CKEDITOR.replace(textarea);
            });
        });
    </script>

    
  



@endsection
