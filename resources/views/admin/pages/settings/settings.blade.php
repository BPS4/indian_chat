@extends('admin.layout.default')

@section('settings','active menu-item-open')
@section('content')
<div class="card card-custom">

    <div class="card-body">
    <div class="settings-container">
        <h2 class="settings-title">Settings</h2>

        <!-- Users Section -->
        <div class="settings-section">
            <div class="settings-header">
                <h6>Users</h6>
                <p class="usesforsettings">Manage all your users and their permissions in the CRM, what they're allowed
                    to do</p>
            </div>
            <div class="settings-grid">
                <div class="settings-card">
                    <div class="settings-icon">
                        <img src="{{asset('media/custom/user-gear-solid.svg')}}" alt="Department">
                    </div>
                    <div>
                        <span class="settings-label"><a href="{{url('/admin/role/list')}}" >Roles</a></span>
                        <p class="settings-desc">Add, edit or delete roles from CRM</p>
                    </div>
                </div>

                <div class="settings-card">
                    <div class="settings-icon">
                        <img src="{{asset('media/custom/circle-user-solid.svg')}}" alt="Department">
                    </div>
                    <div>
                        <span class="settings-label"><a href="{{url('/admin/user/list')}}">Users </a></span>
                        <p class="settings-desc">Add, edit or delete users from CRM</p>
                    </div>
                </div>

                <div class="settings-card">
                    <div class="settings-icon">
                        <img src="{{asset('media/custom/department.svg')}}" alt="Department">
                    </div>
                    <div>
                        <span class="settings-label"><a href="{{url('/admin/department/list')}}">Departments</a></span>
                        <p class="settings-desc">Add, edit or delete departments from CRM</p>
                    </div>
                </div>

                <div class="settings-card">
                    <div class="settings-icon">
                        <img src="{{asset('media/custom/designation.svg')}}" alt="Department">
                    </div>
                    <div>
                        <span class="settings-label"><a href="{{url('/admin/designation/list')}}" >Designations</a></span>
                        <p class="settings-desc">Add, edit or delete designations from CRM</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Automation Section -->
        <div class="settings-section">
            <div class="settings-header">
                <h6>Automation</h6>
                <p class="usesforsettings">Manage all your automation-related settings in the CRM</p>
            </div>
            <div class="settings-grid">
                <div class="settings-card">
                    <div class="settings-icon">
                        <img src="{{asset('media/custom/modules.svg')}}" alt="Department">
                    </div>
                    <div>
                        <span class="settings-label"> <a href="{{url('/admin/module/list')}}">Modules</a></span>
                        <p class="settings-desc">Add, edit or delete modules from CRM</p>
                    </div>
                </div>

                <div class="settings-card">
                    <div class="settings-icon">
                        <img src="{{asset('media/custom/email.svg')}}" alt="Department">
                    </div>
                    <div>
                        <span class="settings-label">Email Templates</span>
                        <p class="settings-desc">Add, edit or delete email templates from CRM</p>
                    </div>
                </div>

                <div class="settings-card">
                    <div class="settings-icon">
                        <img src="{{asset('media/custom/webhooks.svg')}}" alt="Department">
                    </div>
                    <div>
                        <span class="settings-label">Webhooks</span>
                        <p class="settings-desc">Add, edit or delete webhooks from CRM</p>
                    </div>
                </div>

                <div class="settings-card">
                    <div class="settings-icon">
                        <img src="{{asset('media/custom/workflow.svg')}}" alt="Department">
                    </div>
                    <div>
                        <span class="settings-label">Workflows</span>
                        <p class="settings-desc">Add, edit or delete workflows from CRM</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Other Settings Section -->
        <div class="settings-section">
            <div class="settings-header">
                <h6>Other Settings</h6>
                <p class="usesforsettings">Manage all your extra settings in the CRM</p>
            </div>
            <div class="settings-grid">
                <div class="settings-card">
                    <div class="settings-icon">
                        <img src="{{asset('media/custom/webforms.svg')}}" alt="Department">
                    </div>
                    <div>
                        <span class="settings-label">Web Forms</span>
                        <p class="settings-desc">Add, edit or delete web forms from CRM</p>
                    </div>
                </div>

                <div class="settings-card">
                    <div class="settings-icon">
                        <img src="{{asset('media/custom/tags.svg')}}" alt="Department">
                    </div>
                    <div>
                        <span class="settings-label">Tags</span>
                        <p class="settings-desc">Add, edit or delete tags from CRM</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>

@endsection

{{-- Styles Section --}}
@section('styles')
<style>
    .settings-container {
  width: 100%;
  padding-top: 32px;
}
a{
    color:#000 !important;
}
/* Title */
.settings-title {
  font-size: 24px;
  font-weight: bold;
  color: #333;
  margin-bottom: 8px;
  padding-bottom: 16px;
}

.usesforsettings {
  padding-top: 16px;
  margin-bottom: 16px;
}

/* Section Header */
.settings-header h6 {
  font-size: 18px;
  font-weight: bold;

}

.settings-header p {
  color: #777;
  font-size: 14px;
}

/* Grid Layout */
.settings-grid {
  display: flex;
  flex-direction: row;
  margin-top: 15px;
}

/* Individual Setting Cards */
.settings-card {
  display: flex;
  align-items: center;
  background-color: #FEF6E7;

  padding: 16px;
  width: 25%;
  min-width: 250px;
  text-align: left;
  transition: all 0.3s ease-in-out;
}

.settings-card:hover {

  cursor: pointer;
  background-color: #fff5e6;
}

/* Icons */
.settings-icon {
  font-size: 30px;
  color: #b31217;
  background-color: #E7E7E7;
  display: flex;
  justify-content: center;
  align-items: center;
  border-radius: 5px;
  height: 50px;
  width: 50px;
  margin-right: 15px;
}

.settings-icon img {
  width: 30px;
  height: 30px;
}

/* Labels & Descriptions */
.settings-label {
  font-weight: 600;
  font-size: 16px;
  display: block;
}

.settings-desc {
  color: #888;
  font-size: 12px;
  margin-top: 5px;
}

.settings-section {
  margin-top: 16px;
  margin-bottom: 16px;
}
</style>
@endsection

{{-- Scripts Section --}}
@section('scripts')
@endsection
