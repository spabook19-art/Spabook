<script>
    let html = `<div>
                    <div class="header-box px-4 pt-3 pb-4 d-flex justify-content-between">
                        <img src="../../vendor/images/spabookwithtitle.png" alt="SpaBook Logo" class="app_sidebar_logo" style="height:50px;">
                        <button class="btn app_close_sidebar_btn d-md-none d-block px-1 py-0 text-white">
                            <i class="fa-solid fa-bars-staggered"></i>
                        </button>
                    </div>`;
    html += `<ul class="list-unstyled px-2">
                    <li class="app_sidebar_item" id="user_home_page">
                       <a href="../user/user_home_page.php" class="app_sidebar_link"><i class="pe-2 bi bi-calendar4-week"></i>Book appointment</a>
                    </li>
                    <li class="app_sidebar_item" id="user_progress_tracker">
                        <a href="../user/user_progress-tracker.php" class="app_sidebar_link"><i class="pe-2 fa-solid fa-arrow-up-right-dots"></i>Progress Tracker</a>
                    </li>
                    <li class="app_sidebar_item" id="user_services">
                        <a href="../user/user_services.php" class="app_sidebar_link"><i class="pe-2 fa-solid fa-hand-sparkles"></i>Services & Products</a>
                    </li>
                    <li class="app_sidebar_item" id="user_history">
                        <a href="../user/user_history.php" class="app_sidebar_link"><i class="pe-2 bi bi-clock-history"></i>History</a>
                    </li>
                    <li class="app_sidebar_item" id="user_notification">
                        <a href="../user/user_notification.php" class="app_sidebar_link"><i class="pe-2 bi bi-bell"></i>Notification</a>
                    </li>
                </ul>
                 </div>`;

    html += `<div class="footer-box px-4 pt-3 pb-4">
                <ul class="list-unstyled px-2" id="profileSkeleton">
                        <li class="app_sidebar_item">
                            <a href="#" class="app_sidebar_link d-flex align-items-center placeholder-glow">
                                <!-- Circle -->
                                <span class="me-2 rounded-circle bg-secondary placeholder"
                                    style="width:32px;height:32px;display:inline-block;"></span>
                                <!-- Name bar -->
                                <span style="width:200px;">
                                    <span class="d-inline-block bg-secondary placeholder w-100 rounded-2"
                                        style="height:2rem;"></span>
                                </span>
                            </a>
                        </li>
                    </ul>
                    <ul class="list-unstyled px-2 d-none" id="profileItem">
                        <li class="app_sidebar_item">
                            <a href="#" class="app_sidebar_link d-flex align-items-center" data-content="user_profile.php">
                                <span class="profile-img-nav rounded-circle me-2"
                                    style="width:32px; height:32px; display:inline-block; overflow:hidden; background:#fff;">
                                    <img id="navProfileImage"
                                        src=""
                                        alt="Profile"
                                        style="width:100%; height:100%; object-fit:cover;">
                                </span>
                                <span id="navProfileName" class="profile-name-nav fw-semibold" style="font-size:1rem;"></span>
                            </a>

                        </li>
                    </ul>
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