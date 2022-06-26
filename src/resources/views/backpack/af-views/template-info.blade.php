@push('after_scripts')
    <script>
        /*

            window.addEventListener('load', function () {
                afTargetingDisplay('rule_type');
                afConfigureTableName();
            })

            function afConfigureTableName(){
                let field = document.getElementById('table_name').value;
                let selected = document.getElementById('table_name').e.options[e.selectedIndex].text;

                if(field){
                    if(selected){
                        afUpdateField('table_name','/af-data/targeting','targeting',selected)
                    } else {
                        afUpdateField('table_name','/af-data/targeting','targeting')
                    }
                }
            }

            function afUpdateField(name, api, target,selected='') {

                let field = document.getElementById(name);
                let url = api + '?' + name + '=' + field.value;

                $.getJSON(url, function (data) {
                    var options = "";

                    $.each(data, function (key, val) {
                        if(selected && selected === key){
                            options = options + "<option value='"+key+"' selected>" + val + "</option>";
                        } else {
                            options = options + "<option value='"+key+"'>" + val + "</option>";
                        }
                    });

                    $('select#' + target).html(options);
                });
            }

            function afTargetingDisplay(name) {
                let field = document.getElementById(name);

                if (field.value === 'Field change' || field.value === 'Field value') {
                    document.getElementById("w_table_name").style.display = "block";
                    document.getElementById("w_field_name").style.display = "block";
                    document.getElementById("w_rule_operator").style.display = "block";
                    document.getElementById("w_rule_value").style.display = "block";
                    document.getElementById("w_targeting").style.display = "block";
                    hideField('rule_script');
                } else if (field.value === 'Custom script'){
                    hideField('table_name');
                    hideField('field_name');
                    hideField('targeting');
                    document.getElementById("w_rule_operator").style.display = "block";
                    document.getElementById("w_rule_script").style.display = "block";
                    document.getElementById("w_rule_value").style.display = "block";
                } else {
                    hideField('table_name');
                    hideField('field_name');
                    hideField('rule_value');
                    hideField('rule_operator');
                    hideField('rule_script');
                    document.getElementById("w_table_name").style.display = "block";
                    document.getElementById("w_targeting").style.display = "block";
                }
            }

            function hideField(field){
                document.getElementById("w_"+field).style.display = "none";

                if(document.getElementById(field)){
                    //document.getElementById(field).value = '';
                }
            }

        */


        window.addEventListener('load', function () {
            afLoadTables();
            afMasterTemplate();
            afLoadUserRelationships();
        });

        function afMasterTemplateToggle() {

            let indicator = document.getElementById("w_parent_template").style.display;

            if (indicator === "none") {
                document.getElementById("w_parent_template").style.display = "block";
                document.getElementById("w_url_format").style.display = "block";
                document.getElementById("w_email_subject").style.display = "block";
                document.getElementById("af-non-master").style.display = "block";
                document.getElementById("af-master").style.display = "none";
            } else {
                document.getElementById("w_parent_template").style.display = "none";
                document.getElementById("w_url_format").style.display = "none";
                document.getElementById("w_email_subject").style.display = "none";
                document.getElementById("af-non-master").style.display = "none";
                document.getElementById("af-master").style.display = "block";
            }
        }

        function afMasterTemplate() {
            let master = document.getElementById('master_template').value;

            if (master === '1') {
                document.getElementById("w_parent_template").style.display = "none";
                document.getElementById("af-non-master").style.display = "none";
                document.getElementById("w_email_subject").style.display = "none";
                document.getElementById("af-master").style.display = "block";
                document.getElementById("w_url_format").style.display = "none";
            } else {
                document.getElementById("w_parent_template").style.display = "block";
                document.getElementById("af-non-master").style.display = "block";
                document.getElementById("w_email_subject").style.display = "block";
                document.getElementById("af-master").style.display = "none";
                document.getElementById("w_url_format").style.display = "block";
            }
        }

        function afLoadTables() {
            let url = '/af-data/tables';
            $.getJSON(url, function (data) {
                var options = "";

                $.each(data, function (key, val) {
                    options = options + "<option>" + val + "</option>";
                });

                $('select#af-relationship-browser').html(options);
            });
        }

        function afLoadRelationships() {
            let selection = document.getElementById('af-relationship-browser').value;
            let url = '/af-data/relationships?table_name=' + selection;
            $.getJSON(url, function (data) {
                var options = "";

                $.each(data, function (key, val) {
                    options = options + val + ", ";
                });

                $('div#af-table-list').html(options.slice(0, -2));
            });
        }

        function afLoadUserRelationships() {
            let url = '/af-data/relationships?table_name=AfUsers';
            $.getJSON(url, function (data) {
                var options = "";

                $.each(data, function (key, val) {
                    options = options + val + ", ";
                });

                $('div#af-table-list-users').html(options.slice(0, -2));
            });
        }

        /*
        * Updates another field
        * */
        function afUpdateField(name, api, target) {

            let field = document.getElementById(name);
            let url = api + '?' + name + '=' + field.value;

            $.getJSON(url, function (data) {
                var options = "";

                $.each(data, function (key, val) {
                    options = options + "<option>" + val + "</option>";
                });

                $('select#' + target).html(options);
            });
        }
    </script>
@endpush

<style>
    b {
        color: #298A2D;;
    }
</style>


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

    <h1 style="font-size: 22px;">Templating Help</h1>


    <div style="border-color: #516d7b;border-width: 1px;border-radius: 4px;padding: 0 15px 15px 15px;margin-bottom: 20px;margin-top: 15px;">
        <br><b></b><label>Browse table relationships (shows relationship names)</label></b>
        <select name="relationship-browser" class="form-control" id="af-relationship-browser"
                onchange="afLoadRelationships();">
            <option value="">Loading ..</option>
        </select>
        <div id="af-table-list" style="font-weight: bold;color:#298A2D;"></div>
        <br>
        Please note that the context of a rule is relevant when determining the relationships. As an example, if rule is
        triggered by database table "users",
        you can follow relationships from that model.

        Main model has a variable of the same name. <br><br>

        Variable always has the models name (as in the above list). So if the notification is triggered from user model,
        the email would be accessible from <b>@php echo('{{$user->email}}'); @endphp</b> and any related record through
        relation name. So for example <b>@php echo('{{$user->team->name}}'); @endphp</b>.
        If you can't see your models, make sure you've defined models path in the app config.
    </div>

    <div style="border-color: #516d7b;border-width: 1px;border-radius: 4px;padding: 12px 15px 15px 15px;margin-bottom: 20px;margin-top: 15px;">
        Always available variables, based on the notifications: <br><b>@php echo('{{$creator}}->');@endphp</b> - All
        fields
        of a user who initiated the notification event.
        <br><br><b>@php echo('{{$AfEvent->AfCategories->name}}, {{$AfEvent->AfCategories->description}}, {{$AfEvent->AfCategories->icon}}');@endphp</b>
        - Fields from category.
        <br><br><b>@php echo('{{$AfEvent->created_at}}, {{$AfEvent->operation}}, {{$AfEvent->dbtable}}, {{$AfEvent->dbfield}}, {{$AfEvent->dbkey}}');@endphp</b>
        - Fields of the event itself.
        <br><br>Especially the $AfEvent->dbkey is important, as you would use that to build a url to the record itself.
    </div>
    Templates are in Laravel Blade format. They are fed with var replacement and data replacement. Idea is that you can
    dump data from your database record and it's relations directly to the template. So you could define it like this:
    <br>
    <div class="af-code">
        @php echo(htmlspecialchars( 'You have a new notification, click <a href="{{$url ?? \'\'}}">here</a> to read it.')); @endphp
    </div>

    So also this would work:
    <br>
    <div class="af-code">
        @php echo( htmlspecialchars('@if(isset($username) AND $username)) Hello {{$username}}! @endif<br>
            You have a new notification, click <a href="{{$url ?? \'\'}}">here</a> to read it.')); @endphp
    </div>

    And this (provided the correct relations exist):
    <br>
    <div class="af-code">
        @php echo(htmlspecialchars('@if(isset($user->profile) AND $user->profile)) Hello {{$user->profile->name}}! @endif')); @endphp
        <br>
        You have a new notification, click <a href="{{$url ?? ''}}">here</a> to read it.
    </div>
    The variable replacement happens at save time and is "blind" so you should adjust your templates accordingly and
    make sure you mark
    all variables as optional.
    <br><br>
    Notification targeting and channels are defined by rules and custom scripts.
    <br><br>

    <a href="https://laravel.com/docs/9.x/blade" style="font-weight: bold;color:#2E3CA6;">Get to know the Blade
        syntax</a>

</div>