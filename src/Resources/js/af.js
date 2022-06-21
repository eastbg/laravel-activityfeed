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
        /*
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
        */
    }

    wrapper.style.display = "none";
    value = '';
    element.value = value;
    element_passer.value = value;
    return true;
}