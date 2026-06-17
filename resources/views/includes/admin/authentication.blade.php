<div class="container-fluid" style="padding-bottom:0px">
    <h3 style="margin-top:50px;font-size:22px" id="authentication-settings">Authentication</h3> 
    <!-- Authentication -->
    <div class="adminContent" id="authentication">
        @include('includes.response-handling', ['section' => 'authentication-settings'])
        <p id="authentication-output" class="last-edit-T" hidden></p>
        <table>
            <tbody>
                <tr>
                    <td class="align-middle" style="margin-left:25px;margin-right:10px">
                        <p style="min-height:max-content;margin:0px" class="align-middle title" title="Enable the 'register' self sign up page for locally authenticated users at '{{ route('register') }}'.">Self sign up allowed:</p>
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
                            <input type="checkbox" name="enable_2fa" onchange="authSettings(this, 'two_factor_enabled')" @if ((int)$head_data['config']['two_factor_enabled'] == 1) checked @endif>
                            <span class="sliderBlue round align-middle" style="transform: scale(0.8, 0.8)"></span>
                        </label>
                    </td>
                    <td class="align-middle" style="margin-left:25px;margin-right:10px">
                        <p style="min-height:max-content;margin:0px" class="align-middle title" title="Enforce the use of 2FA for ALL users (except Root)">Enforce 2FA:</p>
                    </td>
                    <td class="align-middle" style="padding-left:5px;padding-right:50px" id="enforce_2fa_toggle">
                        <label class="switch align-middle" style="margin-bottom:0px;margin-top:3px" >
                            <input type="checkbox" name="enforce_2fa" onchange="authSettings(this, 'two_factor_enforced')" @if ((int)$head_data['config']['two_factor_enforced'] == 1) checked @endif>
                            <span class="sliderBlue round align-middle" style="transform: scale(0.8, 0.8)"></span>
                        </label>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>