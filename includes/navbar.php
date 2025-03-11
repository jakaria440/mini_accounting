<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
    <div class="container" style="
    max-width: 920px;
    background-color: #f8f9fa;
    padding: 2px;
    border-radius: 10px;
    margin-top: 10px;">
        <a class="navbar-brand d-flex align-items-center" href="/">
            <img src="/assets/logo.png" alt="Logo" width="50" height="50">
            <span class="ms-2 fs-4">আল-বারাকাহ </span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?= $uri === '' ? 'active' : '' ?>" href="/">হোম</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $uri === 'about' ? 'active' : '' ?>" href="/about">আমাদের সম্পর্কে</a>
                </li>
                <?php if(empty($_SESSION['members_id'])): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $uri === 'application' ? 'active' : '' ?>" href="/application">সদস্যতার আবেদন করুন</a>
                </li>
                <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link <?= $uri === 'profile' ? 'active' : '' ?>" href="/profile">আমার প্রোফাইল</a>
                </li>
                <?php endif; ?>
                
                <?php if(isset($_SESSION['members_id']) && ($_SESSION['members_id'] == 1001 || $_SESSION['members_id'] == 1002)): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $uri === 'applications' ? 'active' : '' ?>" href="/applications">আবেদন সমুহ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $uri === 'deposit' ? 'active' : '' ?>" href="/deposit">জমা করুন</a>
                </li>
                <?php endif; ?>
                
                <li class="nav-item">
                    <a class="nav-link <?= $uri === 'contact' ? 'active' : '' ?>" href="/contact">যোগাযোগ</a>
                </li>
                
                <?php if(isset($_SESSION['members_id'])): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $uri === 'logout' ? 'active' : '' ?>" href="/logout">লগআউট </a>
                </li>
                <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link <?= $uri === 'login' ? 'active' : '' ?>" href="/login">লগইন</a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Add this style section after the navbar -->
<style>
    body {
        padding-top: 80px;
    }
    .navbar {
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .navbar-brand img {
        object-fit: contain;
    }
    @media (max-width: 991px) {
        .navbar-nav {
            padding: 1rem 0;
        }
        .nav-item {
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }
        .nav-item:last-child {
            border-bottom: none;
        }
        .navbar-collapse {
            background: white;
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
        }
    }
</style>