<script>
    let html = `<div>
                    <div class="header-box px-4 pt-3 pb-4 d-flex justify-content-between">
                        <img src="../../vendor/images/spabookwithtitle.png" alt="SpaBook Logo" class="app_sidebar_logo" style="height:50px;">
                        <button class="btn app_close_sidebar_btn d-md-none d-block px-1 py-0 text-white">
                            <i class="fa-solid fa-bars-staggered"></i>
                        </button>
                    </div>
                    <div class="app_sidebar_link text-white fw-bold px-4 py-2 d-flex align-items-center" style="cursor: default;">
                        <i class="pe-2 bi bi-person-gear fs-5 " style="font-size: 1.25rem;"></i>
                        Admin Panel
                    </div>`;
    html += `<ul class="list-unstyled px-2">
                    <li class="app_sidebar_item" id="admin_home_page">
                        <a href="../admin/admin_home_page.php" class="app_sidebar_link"><i class="pe-2 bi bi-columns-gap"></i>Dashboard</a>
                    </li>
                    <li class="app_sidebar_item" id="admin_booking_request">
                         <a href="../admin/admin_booking-request.php" class="app_sidebar_link"><i class="pe-2 fa-solid fa-file-pen"></i>Booking Request</a>
                    </li>
                    <li class="app_sidebar_item" id="admin_booking_accepted">
                        <a href="../admin/admin_booking-accepted.php" class="app_sidebar_link"><i class="pe-2 bi bi-check2-square"></i>Booking Accepted</a>
                    </li>
                    <li class="app_sidebar_item" id="admin_manage_services">
                        <a href="../admin/admin_manage-services.php" class="app_sidebar_link"><i class="pe-2 bi bi-leaf"></i>Manage Services & Products</a>
                    </li>
                    <li class="app_sidebar_item" id="admin_manage_users">
                        <a href="../admin/admin_manage-users.php" class="app_sidebar_link"><i class="pe-2 bi bi-people"></i>User Management</a>
                    </li>
                    <li class="app_sidebar_item" id="admin_sales_report">
                        <a href="../admin/admin_sales_report.php" class="app_sidebar_link"><i class="pe-2 bi bi-graph-up-arrow"></i>Sales & Commission Report</a>
                    </li>
                    <li class="app_sidebar_item" id="admin_billing">
                        <a href="../admin/admin_billing.php" class="app_sidebar_link"><i class="pe-2 bi bi-receipt"></i>Billing</a>
                    </li>
                </ul>
                 </div>`;

    html += `<div class="footer-box px-4 pt-3 pb-4">
                    <button class="btn px-3 py-1 text-white w-100 app_sidebar_logout_btn" id="logout-btn">
                        <i class=" bi bi-box-arrow-left"></i> Logout
                    </button>
                </div>`;
    $('#app_sidebar_nav').html(html);

    $('.app_sidebar_nav ul li').on('click', function() {
        $('.app_sidebar_nav ul li.active').removeClass('active');
        $(this).addClass('active');
    });
    const app_sidebar_toggle = document.querySelector('.app_open_sidebar_btn');
    app_sidebar_toggle.addEventListener('click', function() {
        document.querySelector('.app_sidebar_nav').classList.toggle('active');
    });
    $('.app_close_sidebar_btn').on('click', function() {
        $('.app_sidebar_nav').removeClass('active');
    });
</script>
<script type="module">
    $('#logout-btn').on('click', async () => {
        const {
            data: sessionData,
            error
        } = await supabase.auth.getSession();

        if (sessionData.session) {
            await supabase.auth.refreshSession(); // refresh if needed
            await supabase.auth.signOut();
            window.location.href = '../../index.php';
        } else {
            console.warn('No active session');
            window.location.href = '../../index.php';
        }
    });
</script>