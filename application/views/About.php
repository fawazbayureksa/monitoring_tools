<?php
$slides = [
    [
        'image'    => base_url('assets/dist/img/photo1.png'),
        'badge'    => 'PT. United Tractors Tbk.',
        'headline' => 'Heavy-Duty Tool Monitoring System',
        'caption'  => 'Cabang Ujung Pandang — Real-time tool borrowing, delivery tracking, and accountability.',
    ],
    [
        'image'    => base_url('assets/dist/img/photo2.png'),
        'badge'    => 'Service Department',
        'headline' => 'Workshop & Remote Site Visibility',
        'caption'  => 'Integrated lifecycle tracking across Makassar, Kendari, Morowali, Routa, and Pomalaa.',
    ],
];

$team = [
    [
        'name'      => 'Julhandiansyah Putra',
        'role'      => 'Service Management Intern',
        'avatar'    => base_url('assets/dist/img/user-4.png'),
        'whatsapp'  => 'https://wa.me/6285756306898',
        'instagram' => 'https://www.instagram.com/fkswjd',
        'linkedin'  => 'https://www.linkedin.com/in/julhan',
    ],
    [
        'name'      => 'Yuli Amelia K.',
        'role'      => 'Service Management Intern',
        'avatar'    => base_url('assets/dist/img/user-3.png'),
        'whatsapp'  => 'https://wa.me/6285756306898',
        'instagram' => 'https://www.instagram.com/',
        'linkedin'  => 'https://www.linkedin.com/',
    ],
    [
        'name'      => 'Kefas Oswald M.',
        'role'      => 'ADM Service',
        'avatar'    => base_url('assets/dist/img/user-1.png'),
        'whatsapp'  => 'https://wa.me/6285756306898',
        'instagram' => 'https://www.instagram.com/',
        'linkedin'  => 'https://www.linkedin.com/',
    ],
    [
        'name'      => 'Richard Noah Massie',
        'role'      => 'Radioman',
        'avatar'    => base_url('assets/dist/img/user-2.png'),
        'whatsapp'  => 'https://wa.me/6285756306898',
        'instagram' => 'https://www.instagram.com/',
        'linkedin'  => 'https://www.linkedin.com/',
    ],
];

$meta = [
    ['label' => 'App Name',     'value' => 'Monitoring Tools'],
    ['label' => 'Release Date', 'value' => 'May 2026'],
    ['label' => 'Organization', 'value' => 'PT. United Tractors Tbk. (Cabang Ujung Pandang)'],
];
?>
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">Information</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>"><i class="feather icon-info"></i></a></li>
                    <li class="breadcrumb-item"><a href="#!">About</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="about-page">

    <section class="about-carousel" id="aboutCarousel" aria-label="Facility Showcase">
        <div class="about-carousel__viewport">
            <?php foreach ($slides as $index => $slide): ?>
                <div class="about-carousel__slide<?= $index === 0 ? ' is-active' : '' ?>">
                    <img class="about-carousel__img" src="<?= html_escape($slide['image']) ?>" alt="<?= html_escape($slide['headline']) ?>">
                    <div class="about-carousel__overlay">
                        <span class="about-carousel__badge"><?= html_escape($slide['badge']) ?></span>
                        <h2 class="about-carousel__headline"><?= html_escape($slide['headline']) ?></h2>
                        <p class="about-carousel__caption"><?= html_escape($slide['caption']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <button type="button" class="about-carousel__btn about-carousel__btn--prev" id="aboutCarouselPrev" aria-label="Previous slide">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        </button>
        <button type="button" class="about-carousel__btn about-carousel__btn--next" id="aboutCarouselNext" aria-label="Next slide">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
        </button>

        <div class="about-carousel__dots" role="tablist" aria-label="Slide controls">
            <?php foreach ($slides as $index => $slide): ?>
                <button type="button"
                        class="about-carousel__dot<?= $index === 0 ? ' is-active' : '' ?>"
                        role="tab"
                        aria-selected="<?= $index === 0 ? 'true' : 'false' ?>"
                        aria-label="Slide <?= $index + 1 ?>"></button>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="about-section" aria-labelledby="aboutTeamHeading">
        <div class="about-section__head">
            <span class="about-section__eyebrow">Service Department Team</span>
            <h3 class="about-section__title" id="aboutTeamHeading">Meet The Developers</h3>
            <p class="about-section__sub">The team behind Monitoring Tools at PT. United Tractors Tbk.</p>
        </div>

        <div class="about-team">
            <?php foreach ($team as $member): ?>
                <article class="about-member">
                    <div class="about-member__avatar-wrap">
                        <img class="about-member__avatar" src="<?= html_escape($member['avatar']) ?>" alt="<?= html_escape($member['name']) ?>">
                    </div>
                    <h4 class="about-member__name"><?= html_escape($member['name']) ?></h4>
                    <span class="about-member__role"><?= html_escape($member['role']) ?></span>

                    <div class="about-member__socials">
                        <a href="<?= html_escape($member['whatsapp']) ?>"
                           class="about-member__social about-member__social--wa"
                           target="_blank"
                           rel="noopener noreferrer"
                           title="WhatsApp"
                           aria-label="WhatsApp <?= html_escape($member['name']) ?>">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
                        </a>
                        <a href="<?= html_escape($member['instagram']) ?>"
                           class="about-member__social about-member__social--ig"
                           target="_blank"
                           rel="noopener noreferrer"
                           title="Instagram"
                           aria-label="Instagram <?= html_escape($member['name']) ?>">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                        </a>
                        <a href="<?= html_escape($member['linkedin']) ?>"
                           class="about-member__social about-member__social--in"
                           target="_blank"
                           rel="noopener noreferrer"
                           title="LinkedIn"
                           aria-label="LinkedIn <?= html_escape($member['name']) ?>">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="about-info" aria-labelledby="aboutDescHeading">
        <div class="about-info__header">
            <div class="about-info__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
            </div>
            <div>
                <h3 class="about-info__title" id="aboutDescHeading">System Description</h3>
                <p class="about-info__sub">Overview & operational purpose of Monitoring Tools</p>
            </div>
        </div>

        <div class="about-meta">
            <?php foreach ($meta as $item): ?>
                <div class="about-meta__item">
                    <span class="about-meta__label"><?= html_escape($item['label']) ?></span>
                    <span class="about-meta__value"><?= html_escape($item['value']) ?></span>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="about-info__prose">
            <p>
                This website was developed to facilitate the process of borrowing, returning, and monitoring <em>heavy equipment tools</em> at <strong>PT. United Tractors Tbk.</strong>
                The purpose of this tools monitoring website is to reduce human errors that frequently occur during manual processes and to simplify the data recording of heavy equipment borrowing and returning.
            </p>
            <p>
                This website consists of several main features, such as a tools monitoring dashboard, searching and adding tool stock, tool borrowing request processes, and the recording of damaged or lost tool reports (BAKK).
                This monitoring system involves users as tool borrowers and administrators who manage the tools and validate the borrowing and returning processes.
            </p>
            <p>
                With this integrated monitoring system, it is expected that the efficiency and accuracy of tool management will increase significantly.
            </p>
        </div>

        <div class="about-help">
            <div class="about-help__content">
                <svg class="about-help__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                <div class="about-help__text">
                    <strong>Need Help?</strong> If you encounter any issues while using this website, please contact the developer team.
                </div>
            </div>
            <a href="https://wa.me/6287771220505" target="_blank" rel="noopener noreferrer" class="about-help__cta">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
                <span>Contact Support</span>
            </a>
        </div>
    </section>

</div>
