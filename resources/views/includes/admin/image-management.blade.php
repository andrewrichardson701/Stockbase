<div class="container" style="padding-bottom:0px">
    <h3 class="clickable" style="margin-top:50px;font-size:22px" id="imagemanagement-settings" onclick="toggleSection(this, 'imagemanagement')">Image Management <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3> 
    <!-- Image Management Settings -->
    <div style="padding-top: 20px" id="imagemanagement" hidden>
        <?php
        // if ((isset($_GET['section']) && $_GET['section'] == 'imagemanagement')) {
        //     showResponse();
        // }
        ?>
        {!! $response_handling !!}
        <div style="max-height:60vh;overflow-x: hidden;overflow-y: auto; margin-left:10px; margin-right:10px">
            <p>Image Count: <or class="green">{{ $image_management_count }}</or></p>
            <table class="table table-dark theme-table" style="max-width:max-content">
                <thead>
                    <tr class="theme-tableOuter">
                        <th class="text-center theme-tableOuter" style="width:130px; position: sticky; top: -1;">Image</th>
                        <th class="text-center theme-tableOuter" style="position: sticky; top: -1;">File</th>
                        <th class="text-center theme-tableOuter" style="position: sticky; top: -1;">Links</th>
                        <th class="text-center theme-tableOuter" style="position: sticky; top: -1; z-index:10">Delete</th>
                        <th class="text-center theme-tableOuter" style="position: sticky; top: -1;">&nbsp;</th>
                    </tr>
                </thead>
                <tbody id="image-management-tbody">
                    <tr id="loader-tr">
                        <td id="loader-td" colspan="100%" class="algin-middle text-center">
                            <div id="loader-outerdiv">
                                <button class="btn btn-info" id="show-images" onclick="loadAdminImages(0, 1)">Load Images</button>
                                <div class="loader" id="loaderDiv" style="margin-top:10px;width:130px;display:none">
                                    <div class="loaderBar"></div>
                                </div>
                            </div>
                        </td>
                    </tr>                        
                </tbody>
            </table>
        </div>
    </div>
</div>