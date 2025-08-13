<div class="container" style="padding-bottom:0px">
    <h3 class="clickable" style="margin-top:50px;font-size:22px" id="footer-settings" onclick="toggleSection(this, 'footer')">Footer <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3> 
    <!-- Footer -->
    <div style="padding-top: 20px" id="footer" hidden>
        <?php
        // if ((isset($_GET['section']) && $_GET['section'] == 'footer')) {
        //     showResponse();
        // }
        ?>
        @include('includes.response-handling', ['section' => 'footer-settings'])
        <div style="max-height:60vh;overflow-x: hidden;overflow-y: auto; margin-left:10px; margin-right:10px">
            <p id="footer-output" class="last-edit-T" hidden></p>
            <table>
                <tbody>
                    <tr>
                        <td class="align-middle" style="margin-left:25px;margin-right:10px" id="normal-footer">
                            <p style="min-height:max-content;margin:0px" class="align-middle title" title="Enable Footer at the bottom of each page.">Enable Footer:</p>
                        </td>
                        <td class="align-middle" style="padding-left:5px;padding-right:20px" id="normal-footer-toggle">
                            <label class="switch align-middle" style="margin-bottom:0px;margin-top:3px">
                                <input type="checkbox" name="normal-footer" onchange="toggleFooter(this, 1)" @if ($head_data['config']['footer_enable'] == 1) checked @endif>
                                <span class="sliderBlue round align-middle" style="transform: scale(0.8, 0.8)"></span>
                            </label>
                        </td>
                        <td class="align-middle" style="margin-left:25px;margin-right:10px" id="left-footer">
                            <p style="min-height:max-content;margin:0px" class="align-middle title" title="Enable GitLab link on the footer.">Enable GitLab Link:</p>
                        </td>
                        <td class="align-middle" style="padding-left:5px;padding-right:20px" id="left-footer-toggle">
                            <label class="switch align-middle" style="margin-bottom:0px;margin-top:3px">
                                <input type="checkbox" name="left-footer" onchange="toggleFooter(this, 2)" @if ($head_data['config']['footer_left_enable'] == 1) checked @endif>
                                <span class="sliderBlue round align-middle" style="transform: scale(0.8, 0.8)"></span>
                            </label>
                        </td>
                        <td class="align-middle" style="margin-left:25px;margin-right:10px" id="right-footer">
                            <p style="min-height:max-content;margin:0px" class="align-middle title" title="Enable Road Map link on the footer.">Enable Road Map Link:</p>
                        </td>
                        <td class="align-middle" style="padding-left:5px;padding-right:20px" id="right-footer-toggle">
                            <label class="switch align-middle" style="margin-bottom:0px;margin-top:3px">
                                <input type="checkbox" name="right-footer" onchange="toggleFooter(this, 3)" @if ($head_data['config']['footer_right_enable'] == 1) checked @endif>
                                <span class="sliderBlue round align-middle" style="transform: scale(0.8, 0.8)"></span>
                            </label>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>