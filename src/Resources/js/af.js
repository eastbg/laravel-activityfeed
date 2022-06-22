/*
* Finds the database columns based on
* */
function updateSelectedTable(name){

    let field = document.getElementById(name);

    $.getJSON('/af-data/fields?table='+field.value, function(data) {
        var options = "";

        $.each(data, function (key, val) {
            options = options + "<option>" + val + "</option>";
        });

        $('select#field_name').html(options);
    });
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
