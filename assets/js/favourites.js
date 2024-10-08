function favouriteStock(stock_id) {
    var star = document.getElementById('favouriteIcon');
    var div = document.getElementById('favouriteButton');

    $.ajax({
        type: "POST",
        url: "includes/favourites.inc.php",
        data: {
            stock_id: stock_id
        },
        dataType: "json",
        success: function(response){
//	    console.log(response);
            // do something with redirect_url to put it on the page.
            if (response['status'] == 'true') {
                if (response['type'] == 'add') {
                    star.classList.remove('fa-regular');
                    star.classList.add('fa-solid');
                } else {
                    star.classList.remove('fa-solid');
                    star.classList.add('fa-regular');
		}
            } else {
		console.log('Failed to adjust favourites.');
	    }
        },
        error: function(response) {
            console.log(response);
        },
        async: true // <- this turns it into synchronous
    });
}