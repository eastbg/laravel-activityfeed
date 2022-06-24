<script>

    window.addEventListener('load', function () {
        afLoadTables();
    })

    function afLoadTables(){
        let url = '/af-data/tables';
        $.getJSON(url, function (data) {
            var options = "";

            $.each(data, function (key, val) {
                options = options + "<option>" + val + "</option>";
            });

            $('select#af-relationship-browser').html(options);
        });
    }

    function afLoadRelationships(){
        let selection = document.getElementById('af-relationship-browser').value;
        let url = '/af-data/relationships?table_name='+selection;
        $.getJSON(url, function (data) {
            var options = "";

            $.each(data, function (key, val) {
                options = options + val + ", ";
            });

            $('div#af-table-list').html(options.slice(0,-2));
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


<h1 style="font-size: 22px;">Templating</h1>


<div style="border-color: #516d7b;border-width: 1px;border-radius: 4px;padding: 0 15px 15px 15px;margin-bottom: 20px;margin-top: 15px;">
    <br><b></b><label>Browse table relationships (shows relationship names)</label></b>
    <select name="relationship-browser" class="form-control" id="af-relationship-browser" onchange="afLoadRelationships();">
        <option value="">Loading .. </option>
    </select>
    <div id="af-table-list"></div>
</div>

Templates are in Laravel Blade format. They are fed with var replacement and data replacement. Idea is that you can dump data from your database record and it's relations directly to the template. So you could define it like this:
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
The variable replacement happens at save time and is "blind" so you should adjust your templates accordingly.
<br><br>
Rules define targeting and channels.
<br><br>

<a href="https://laravel.com/docs/9.x/blade">Blade Syntax</a>

