<?php 
$WorB_banner_color = getWorB($current_banner_color);
$complemenent_banner_color = getComplement($current_banner_color);
$WorB_complement_banner_color = getWorB($complemenent_banner_color);
?>
<style>
    #scrollTop {
        display: block;
        position: fixed;
        bottom: 20px;
        right: -100px;
        z-index: 99;
        font-size: 18px;
        border: none;
        outline: none;
        cursor: pointer;
        overflow: hidden;
    }

    #scrollTop:hover {

    }

    .scrollBtn {
        font-size: 18px;
        border: none;
        outline: none;
        background-color: <?php echo $current_banner_color; ?>;
        color: <?php echo $WorB_banner_color; ?>;
        cursor: pointer;
        padding: 0px 10px 0px 10px;
        border-radius: 4px;
        position: inline-block;
        width: 38px;
        height: 38px;
        transition: width 0.2s ease; /* Apply smooth width and position transitions */
        overflow: hidden;
    }

    .scrollIcon {
        padding: 10px 0px 10px 0px;
    }

    .scrollBtn:hover {
        width: 180px;
        background-color: <?php echo $complemenent_banner_color; ?>;
        color: <?php echo $WorB_complement_banner_color; ?>;
    }
    .scrollBtn span {
        height:25px
    } 

</style>
<div id="scrollTop" class="hideTranslate">
<button onclick="topFunction()" class="scrollBtn" id="scrollBtn" title="Go to top.">
    <i class="fa fa-chevron-up scrollIcon"></i> <span id="scrollText">Scroll to Top</span>
</button>
</div>
<script>
    // SCROLL TO TOP SECTION

    //Get the button
    var mybutton = document.getElementById("scrollTop");

    // When the user scrolls down 20px from the top of the document, show the button
    window.onscroll = function() {
        scrollFunction()
    };

    function scrollFunction() {
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            mybutton.className = "viewTranslate";
            document.getElementById("scrollTop").style.width = "max-content";
        } else {
            mybutton.className = "hideTranslate";
            document.getElementById("scrollTop").style.width = "38px";
        }
    }
    // When the user clicks on the button, scroll to the top of the document
    function topFunction() {
        window.scrollTo({top: 0, behavior: 'smooth'});
    }
    
</script>
<div class="footer">
    <div class="container">
        <div class="row">
            <div class="col text-center" style="padding-top:2px">
                <a href="https://git.ajrich.co.uk/web/inventory" class="link" style="font-size:12px" target="_blank">GitLab</a>
            </div>
            <div class="col-6 text-center">
                Copyright &copy;<?php echo date("Y"); ?> Andrew Richardson. All rights reserved.
            </div>
            <div class="col text-center" style="padding-top:2px">
                <a href="https://todo.ajrich.co.uk/#/board/16" class="link" style="font-size:12px" target="_blank">Road Map</a>
            </div>
        </div>
    </div>
</div>