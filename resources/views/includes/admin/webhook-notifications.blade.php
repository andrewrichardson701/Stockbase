<div class="container" style="padding-bottom:0px">
    <h3 class="clickable" style="margin-top:50px;font-size:22px" id="webhook-settings" onclick="toggleSection(this, 'webhook')">Webhook Notification Settings <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3> 

    <!-- Webhook Settings -->
    <div style="padding-top: 20px" id="webhook" hidden>

        @include('includes.response-handling', ['section' => 'webhook-settings'])

        Work in Progress
        
    </div>
</div>