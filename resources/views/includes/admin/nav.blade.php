<style>
body {
  font-family: "Lato", sans-serif;
}

.sidenav {
  height: 100%;
  width: max-content;
  position: fixed;
  z-index: 1;
  top: 0;
  left: 0;
  overflow-x: hidden;
  padding-top: 147px;
}

.sidenav a {
    color: unset !important;
}

.sidenav h3 {
  padding: 6px 6px 6px 32px;
  text-decoration: none;
  font-size: 20px;
  display: block;
  padding-bottom:20px;
  padding-right:20px;
  margin-bottom:0px;
}

@media screen and (max-height: 450px) {
  .sidenav {padding-top: 15px;}
  .sidenav a {font-size: 18px;}
}
</style>

<div class="sidenav theme-divBg shadow">
    <ul>
        <li class="clickable @if($nav_secondary == 'global') theme-th-selected @endif" >
            <a href="{{ route('admin', ['setting'=>'global']) }}"><h3>Global Settings</h3></a>
        </li>
        <li class="clickable @if($nav_secondary == 'users') theme-th-selected @endif">
            <a href="{{ route('admin', ['setting'=>'users']) }}"><h3>Users</h3></a>
        </li>
        <li class="clickable @if($nav_secondary == 'authentication') theme-th-selected @endif">
            <a href="{{ route('admin', ['setting'=>'authentication']) }}"><h3>Authentication</h3></a>
        </li>
        <li class="clickable @if($nav_secondary == 'image-management') theme-th-selected @endif">
            <a href="{{ route('admin', ['setting'=>'image-management']) }}"><h3>Image Management</h3></a>
        </li>
        <li class=" @if(in_array($nav_secondary, ['stock-attributes','optic-attributes','cpu-attributes','memory-attributes','disk-attributes'])) theme-th-selected @endif"  onclick="toggleMainSection(this, 'attribute-pages')">
            <div class="clickable">
                <h3 style="margin-bottom:0px">Attribute Management <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px;"></i></h3>
            </div>
            <div id="attribute-pages" class="container" style="width:100%; padding-bottom:20px" @if(!in_array($nav_secondary, ['stock-attributes','optic-attributes','cpu-attributes','memory-attributes','disk-attributes'])) hidden @endif>
                <ul>
                    <li class="clickable @if($nav_secondary == 'stock-attributes') theme-th-selected-secondary @endif" style="padding-left:40px">
                        <a href="{{ route('admin', ['setting'=>'stock-attributes']) }}">Stock Attributes</a>
                    </li>
                    <li class="clickable @if($nav_secondary == 'optic-attributes') theme-th-selected-secondary @endif" style="padding-left:40px">
                        <a href="{{ route('admin', ['setting'=>'optic-attributes']) }}">Optic Attributes</a>
                    </li>
                    <li class="clickable @if($nav_secondary == 'cpu-attributes') theme-th-selected-secondary @endif"  style="padding-left:40px">
                        <a href="{{ route('admin', ['setting'=>'cpu-attributes']) }}">CPU Attributes</a>
                    </li>
                    <li class="clickable @if($nav_secondary == 'memory-attributes') theme-th-selected-secondary @endif" style="padding-left:40px">
                        <a href="{{ route('admin', ['setting'=>'memory-attributes']) }}">Memory Attributes</a>
                    </li>
                    <li class="clickable @if($nav_secondary == 'disk-attributes') theme-th-selected-secondary @endif" style="padding-left:40px">
                        <a href="{{ route('admin', ['setting'=>'disk-attributes']) }}">Disk Attributes</a>
                    </li>
                </ul>
            </div>
            
        </li>
        <li class="clickable @if($nav_secondary == 'stock-management') theme-th-selected @endif">
            <a href="{{ route('admin', ['setting'=>'stock-management']) }}"><h3>Stock Management</h3></a>
        </li>
        <li class="clickable @if($nav_secondary == 'stock-locations') theme-th-selected @endif">
            <a href="{{ route('admin', ['setting'=>'stock-locations']) }}"><h3>Stock Locations</h3></a>
        </li>
        <li class="clickable @if($nav_secondary == 'email') theme-th-selected @endif">
            <a href="{{ route('admin', ['setting'=>'email']) }}"><h3>Email Notifications</h3></a>
        </li>
        <li class="clickable @if($nav_secondary == 'webhook') theme-th-selected @endif">
            <a href="{{ route('admin', ['setting'=>'webhook']) }}"><h3>Webhook Notifications</h3></a>
        </li>
        <li class="clickable @if($nav_secondary == 'changelog') theme-th-selected @endif">
            <a href="{{ route('admin', ['setting'=>'changelog']) }}"><h3>Changelog</h3></a>
        </li>
    </ul>
  
  
</div>   