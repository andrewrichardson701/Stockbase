<div id="modalDivResetPW" class="modal" style="display: none;">
    <span class="close" onclick="modalCloseResetPW()">×</span>
    <div class="container well-nopad theme-divBg" style="padding:25px">
        <div style="margin:auto;text-align:center;margin-top:10px">
            <form action="/admin.userSettings" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="admin_pwreset_submit" value="set" />
                <input type="hidden" name="user_id" id="modal-user-id" value=""/>
                <table class="centertable">
                    <tbody>
                        <tr>
                            <td class="align-middle" style="padding-right:20px">
                                New Password:
                            </td>
                            <td class="align-middle" style="padding-right:20px">
                                <input type="password" name="password" id="reset-password" required>
                            </td>
                            <td class="align-middle">
                                <input type="submit" name="submit" class="btn btn-success" value="Change">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
</div>