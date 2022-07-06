@push('before_scripts')
    <script>

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

        function showRelationships() {
            let relations = document.getElementById('af-table-list');
            let fields = document.getElementById('af-field-list');

            let navi_relations = document.getElementById('nav-relationships');
            let navi_fields = document.getElementById('nav-fields');

            fields.style.display = 'none';
            relations.style.display = 'block';

            navi_relations.className = "nav-link active";
            navi_fields.className = "nav-link";
        }

        function showFields() {
            let relations = document.getElementById('af-table-list');
            let fields = document.getElementById('af-field-list');

            let navi_relations = document.getElementById('nav-relationships');
            let navi_fields = document.getElementById('nav-fields');

            fields.style.display = 'block';
            relations.style.display = 'none';

            navi_relations.className = 'nav-link';
            navi_fields.className = 'nav-link active';
        }

        function maintabInstructions() {
            document.getElementById('af-maintab-instructions').style.display = 'block';
            document.getElementById('af-maintab-notification-preview').style.display = 'none';
            document.getElementById('af-maintab-msg-preview').style.display = 'none';

            document.getElementById('af-nav-instructions').className = "nav-link active";
            document.getElementById('af-nav-notification-preview').className = "nav-link";
            document.getElementById('af-nav-msg-preview').className = "nav-link";
        }

        function maintabNotificationPreview() {
            document.getElementById('af-maintab-instructions').style.display = 'none';
            document.getElementById('af-maintab-notification-preview').style.display = 'block';
            document.getElementById('af-maintab-msg-preview').style.display = 'none';

            document.getElementById('af-nav-instructions').className = "nav-link";
            document.getElementById('af-nav-notification-preview').className = "nav-link active";
            document.getElementById('af-nav-msg-preview').className = "nav-link";

            afUpdatePreviews();
        }

        function maintabMsgPreview() {
            document.getElementById('af-maintab-instructions').style.display = 'none';
            document.getElementById('af-maintab-notification-preview').style.display = 'none';
            document.getElementById('af-maintab-msg-preview').style.display = 'block';

            document.getElementById('af-nav-instructions').className = "nav-link";
            document.getElementById('af-nav-notification-preview').className = "nav-link";
            document.getElementById('af-nav-msg-preview').className = "nav-link active";

            afUpdatePreviews();

        }

        function afLoadTableInfo() {

            document.getElementById('af-relationship-browser').value
            let selection = document.getElementById('af-relationship-browser').value;
            let url = '/af-data/tableInfo?table_name=' + selection;
            document.getElementById('af-helpers').style.display = 'block';

            $.getJSON(url, function (data) {
                var relations = "";
                var fields = "";

                $.each(data['relations'], function (key, val) {
                    relations = relations + val + ", ";
                });

                $.each(data['fields'], function (key, val) {
                    fields = fields + val + ", ";
                });

                $('div#af-table-list').html(relations.slice(0, -2));
                $('div#af-field-list').html(fields.slice(0, -2));
            });

            showRelationships();
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

/*
        function afUpdatePreviews(){
            let data = document.getElementById('admin_template').value;
            data = varReplacer(data);
            $('div#af-preview-admin').html(data);
        }

        function varReplacer(data){

            let url = '/af-data/var-replacer';

            let postfields = {
                'data': data
            }

            $.getJSON(url, postfields, function (response) {
                $('div#af-preview-admin').html(response);
            });

            return data;
        }
*/




        function afUpdatePreviews(){
            let nt = document.getElementById('notification_template').value;
            varReplacer(nt,'af-preview-notification','');

            let at = document.getElementById('admin_template').value;
            varReplacer(at,'af-preview-admin','admin-');

            let dt = document.getElementById('digest_template').value;
            varReplacer(dt,'af-preview-digest','');

            let em = document.getElementById('email_template').value;
            varReplacer(em,'af-preview-email','email-');

/*
            $('div#af-preview-notification').html(varReplacer(nt));

            varReplacer(nt,'af-preview-notification');

            let at = document.getElementById('admin_template').value;
            varReplacer(at,'af-preview-admin');

            let dg = document.getElementById('digest_template').value;
            varReplacer(dg,'af-preview-digest');
*/
        }

        function varReplacer(data,target,template){

            let url = '/af-data/var-replacer';

            let postfields = {
                'data': data,
                'id': {{$id_parent ?? null}},
                'template': template
            }

            $.getJSON(url, postfields, function (response) {
                $('div#'+target).html(response);
            });
        }

    </script>
@endpush