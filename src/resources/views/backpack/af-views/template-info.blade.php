@include('backpack.af-views.af-template-scripts')
@include('backpack.af-views.af-template-styles')

<div id="af-master">
    <h1 style="font-size: 22px;">Master templates</h1>

    Master templates are basically decorators. Important thing is to include in your master template a variable: <br>
    <br>
    <b>@php echo('{!! $content !!}'); @endphp</b><br>
    <br>
    This single variable marks the space where the "real" template content will be put.
    For the time being, we are not supporting more advanced functionalities with master templates.
    <br><br>
    Additional objects that are available:
    <br>
    <b>$user</b> - Recipient user object<br>
    <b>$creator</b> - Notification originator OR default owner, depending on the rule setup<br>

    <br>
    User object relationships:
    <br>
    <div id="af-table-list-users" style="font-weight: bold;color:#298A2D;"></div>
</div>

<div id="af-non-master">

    <ul class="nav nav-tabs">
        <li class="nav-item active">
            <a class="nav-link active" id="af-nav-instructions" aria-current="page"
               href="javascript:maintabInstructions();">Templating</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="af-nav-notification-preview" href="javascript:maintabNotificationPreview();">Preview notification</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="af-nav-msg-preview" href="javascript:maintabMsgPreview();">Preview msg</a>
        </li>
    </ul>

    <div id="af-maintab-instructions">



        @include('backpack.af-views.template-instructions')
    </div>

    <div id="af-maintab-notification-preview" style="display: none;">
        @include('backpack.af-views.template-notify-preview')
    </div>

    <div id="af-maintab-msg-preview" style="display: none;">
        @include('backpack.af-views.template-msg-preview')
    </div>

</div>