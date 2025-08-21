<?php  
// This file is part of StockBase.
// StockBase is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// StockBase is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// You should have received a copy of the GNU General Public License along with StockBase. If not, see <https://www.gnu.org/licenses/>.

// $WorB_banner_color = getWorB($current_banner_color);
// $complemenent_banner_color = getComplement($current_banner_color);
// $WorB_complement_banner_color = getWorB($complemenent_banner_color);
?>
<style>
    .scrollBtn {
        background-color: {{$head_data['config_compare']['banner_color']}};
        color: {{$head_data['extras']['banner_text_color']}};
    }

    .scrollBtn:hover {
        background-color: {{$head_data['extras']['invert_banner_color']}};
        color: {{$head_data['extras']['invert_banner_text_color']}};
    }

</style>
<div id="scrollTop" class="hideTranslate">
    <button onclick="topFunction()" class="scrollBtn" id="scrollBtn" title="Go to top.">
        <i class="fa fa-chevron-up scrollIcon"></i> &nbsp;<span id="scrollText">Scroll to Top</span>
    </button>
</div>


@if ($head_data['config_compare']['footer_enable'] == 1)
<div class="footer-spacer" style="height:20px">
</div>
<div class="footer theme-footer">
    <div class="container">
        <div class="row">
            <div class="col text-center viewport-large-empty">
            @if ($head_data['config_compare']['footer_left_enable'] == 1)
                <a href="https://github.com/andrewrichardson701/stockbase" class="link" style="font-size:12px" target="_blank">GitHub</a>
            @endif
            </div>                
            <div class="col-6 text-center viewport-large-block" style="font-size:12px;cursor:pointer;" onclick="window.location.href='{{ url('about') }}'">
                Copyright &copy; {{ now()->year }} StockBase. All rights reserved.
            </div>
            <div class="col text-center viewport-large-empty">
            @if ($head_data['config_compare']['footer_right_enable'] == 1)
                    <a href="https://github.com/andrewrichardson701/stockbase#roadmap" class="link" style="font-size:12px" target="_blank">Road Map</a>
            @endif
            </div>
        </div>
    </div> 
    <div class="align-right popupBox-owner" style="display: block;position: absolute;bottom: 4px;right: 20px;z-index: 99;font-size: 18px;border: none;outline: none;cursor: pointer;overflow: hidden;">
        <a href="about" style="font-size:12px" id="version-about">@if (isset($head_data['update_data']['update_available']) && $head_data['update_data']['update_available'] ==1) <i class="fa-solid fa-circle-exclamation" style="color: #ff3000; margin-right:7px"></i> @endif {{$head_data['version_number']}}</a>
    </div>
</div>
    @if (isset($head_data['user']['id']) && ($head_data['user']['permissions']['root'] == 1 || $head_data['user']['permissions']['admin'] == 1))
    <span id="version-check" class="popupBox well-nopad text-center theme-divBg">
        @if (isset($head_data['update_data']['update_text']))
            {!! $head_data['update_data']['update_text'] !!}
        @else
            Unable to check for updates.
        @endif
    </span>
    @endif
@endif  

<?php

?>

<!-- Add the JS for the file -->
<script src="{{ asset('js/foot.js') }}"></script>