<div class="container" style="padding-bottom:0px">
    <h3 class="clickable" style="margin-top:50px;font-size:22px" id="authentication-settings" onclick="toggleSection(this, 'authentication')">Authentication <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3> 
    <!-- Authentication -->
    <div style="padding-top: 20px" id="authentication" hidden>
        <?php
        // if ((isset($_GET['section']) && $_GET['section'] == 'authentication')) {
        //     showResponse();
        // }
        ?>
        @include('includes.response-handling')
        <p id="authentication-output" class="last-edit-T" hidden></p>
        <table>
            <tbody>
                <tr>
                    <td class="align-middle" style="margin-left:25px;margin-right:10px">
                        <p style="min-height:max-content;margin:0px" class="align-middle title" title="Enable the signup.php self sign up page for locally authenticated users">Self sign up allowed:</p>
                    </td>
                    <td class="align-middle" style="padding-left:5px;padding-right:50px" id="signup_allowed_toggle">
                        <label class="switch align-middle" style="margin-bottom:0px;margin-top:3px" >
                            <input type="checkbox" name="signup_allowed" onchange="authSettings(this, 'signup_allowed')" @if ((int)$head_data['config']['signup_allowed'] == 1) checked @endif>
                            <span class="sliderBlue round align-middle" style="transform: scale(0.8, 0.8)"></span>
                        </label>
                    </td>
                    <td class="align-middle" style="margin-left:25px;margin-right:10px">
                        <p style="min-height:max-content;margin:0px" class="align-middle">Enable 2FA:</p>
                    </td>
                    <td class="align-middle" style="padding-left:5px;padding-right:50px" id="enable_2fa_toggle">
                        <label class="switch align-middle" style="margin-bottom:0px;margin-top:3px" >
                            <input type="checkbox" name="enable_2fa" onchange="authSettings(this, '2fa_enabled')" @if ((int)$head_data['config']['2fa_enabled'] == 1) checked @endif>
                            <span class="sliderBlue round align-middle" style="transform: scale(0.8, 0.8)"></span>
                        </label>
                    </td>
                    <td class="align-middle" style="margin-left:25px;margin-right:10px">
                        <p style="min-height:max-content;margin:0px" class="align-middle title" title="Enforce the use of 2FA for ALL users (except Root)">Enforce 2FA:</p>
                    </td>
                    <td class="align-middle" style="padding-left:5px;padding-right:50px" id="enforce_2fa_toggle">
                        <label class="switch align-middle" style="margin-bottom:0px;margin-top:3px" >
                            <input type="checkbox" name="enforce_2fa" onchange="authSettings(this, '2fa_enforced')" @if ((int)$head_data['config']['2fa_enforced'] == 1) checked @endif>
                            <span class="sliderBlue round align-middle" style="transform: scale(0.8, 0.8)"></span>
                        </label>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>