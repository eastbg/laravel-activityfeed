window.addEventListener('load', function () {
    afTargetingDisplay('table_name');
})

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

function afTargetingDisplay(name) {
    let field = document.getElementById(name);

    if (field.value === 'Field change' || field.value === 'Field value') {
        document.getElementById("w_table_name").style.display = "block";
        document.getElementById("w_field_name").style.display = "block";
        document.getElementById("w_rule_operator").style.display = "block";
        document.getElementById("w_rule_value").style.display = "block";
        document.getElementById("w_targeting").style.display = "block";
        document.getElementById("w_rule_script").style.display = "none";
    } else if (field.value === 'Custom script'){
        document.getElementById("w_table_name").style.display = "none";
        document.getElementById("w_field_name").style.display = "none";
        document.getElementById("w_targeting").style.display = "none";

        document.getElementById("w_rule_operator").style.display = "block";
        document.getElementById("w_rule_script").style.display = "block";
        document.getElementById("w_rule_value").style.display = "block";
    } else {
        document.getElementById("w_field_name").style.display = "none";
        document.getElementById("w_rule_operator").style.display = "none";
        document.getElementById("w_rule_value").style.display = "none";
        document.getElementById("w_table_name").style.display = "none";
        document.getElementById("w_rule_script").style.display = "none";

        document.getElementById("w_targeting").style.display = "block";
    }
}


/*
    let field = document.getElementById(name);

    var options = [
        {text: "one", value: 1},
        {text: "two", value: 2}
    ];

    $("#field_name").replaceOptions(options);
*/

/*
    $.getJSON('/af-data/fields?table='+field.value, function(data) {

        var options = "";
        //$("#field_name").empty();

        $.each(data, function(key, val){
            options = options+"<option>"+val+"</option>";

            var option = $('<option></option>').attr(key, val).text(val);
            $("#field_name").append(option);

        });

        $('select#field_name').html(options);


        $("#field_name").empty().append(options);

        $('select#field_name').html(options);
        });
*/


/*

const dateUpdated = function(){
    // update the last

    let updatedDate = new Date().toLocaleDateString('en-CA');

    document.getElementById("Availability_Updated").value = updatedDate;
    document.getElementById("Availability_Updated_passer").value = updatedDate;
}

const changeDate = function() {

    dateUpdated();

    let selection = document.getElementById("Availability").value;

    let element = document.getElementById("Available_From");
    let element_passer = document.getElementById("Available_From_passer");
    let wrapper = document.getElementById("Available_From-wrapper");

    let date = new Date();
    let numberOfDaysToAdd = 0;
    let value;

    value = new Date(date).toLocaleDateString('en-CA');

    switch(selection){
        case 'Available from a Specific Date':
            wrapper.style.display = "block";
            numberOfDaysToAdd = 14;
            return true;
        /!*
                        case 'Available in 7 days after confirmation':
                            wrapper.style.display = "none";
                            numberOfDaysToAdd = 10;
                            return true;
                        case 'Available in 14 days after confirmation':
                            wrapper.style.display = "none";
                            numberOfDaysToAdd = 18;
                            return true;
                        case 'Available in 30 days after confirmation':
                            wrapper.style.display = "none";
                            numberOfDaysToAdd = 34;
                            return true;
                        case 'Available in 60 days after confirmation':
                            wrapper.style.display = "none";
                            numberOfDaysToAdd = 64;
                            return true;
                        case 'Not available':
                            wrapper.style.display = "none";
                            numberOfDaysToAdd = 356;
                            return true;
        *!/
    }

    wrapper.style.display = "none";
    value = '';
    element.value = value;
    element_passer.value = value;
    return true;
}*/
