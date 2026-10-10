<style>
    .hh-persona-avatar {
        width: 72px;
        height: 72px;
        flex: 0 0 72px;
        border-radius: 50%;
        object-fit: cover;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(115, 102, 255, .10);
        border: 2px solid rgba(115, 102, 255, .15);
        color: var(--theme-default, #7366ff);
        font-size: 25px;
        font-weight: 700;
    }
    .hh-persona-avatar.lg {
        width: 104px;
        height: 104px;
        flex-basis: 104px;
        font-size: 38px;
        border: 4px solid #fff;
        box-shadow: 0 3px 20px rgba(23, 31, 55, .1);
    }
    .hh-persona-avatar.sm {
        width: 52px;
        height: 52px;
        flex-basis: 52px;
        font-size: 18px;
    }
    .hh-persona-card { height: 100%; transition: transform .15s ease, box-shadow .15s ease; }
    .hh-persona-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(32, 41, 62, .08); }
    .hh-persona-card .card-body { display: flex; flex-direction: column; }
    .hh-persona-card .person-detail { color: #707782; font-size: .88rem; }
    .hh-persona-name { overflow-wrap: anywhere; }
    .hh-ficha-cover {
        min-height: 124px;
        border-radius: 14px 14px 0 0;
        background: linear-gradient(120deg, var(--theme-default, #7366ff), #a69efa);
    }
    .hh-ficha-identity { margin-top: -53px; position: relative; }
    .hh-stat { background: rgba(115, 102, 255, .05); border-radius: 10px; padding: 15px; min-height: 92px; }
    .hh-info-label { font-size: .8rem; color: #7e8694; margin-bottom: 5px; }
    .hh-info-value { font-weight: 600; overflow-wrap: anywhere; }
    .hh-profile-tabs .nav-link { text-align: left; width: 100%; display: flex; align-items: center; gap: 12px; padding: 13px 14px; border-radius: 10px; }
    .hh-profile-tabs .nav-link:not(.active) { color: inherit; }
    .hh-profile-tabs i { width: 23px; text-align: center; font-size: 19px; }
    .hh-profile-tabs .healthicons-blood-bag { font-size: 23px; }
    .hh-timeline { border-left: 2px solid #e8e7f6; margin-left: 10px; }
    .hh-timeline-item { position: relative; padding-left: 22px; margin-bottom: 22px; }
    .hh-timeline-item::before { content: ''; position: absolute; left: -7px; top: 4px; height: 12px; width: 12px; background: var(--theme-default, #7366ff); border: 2px solid white; border-radius: 50%; box-shadow: 0 0 0 1px #d6d1ff; }
    .hh-placeholder { min-height: 205px; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 22px; }
    .hh-placeholder i { font-size: 34px; opacity: .5; margin-bottom: 15px; }
    .hh-history-values { display: flex; flex-wrap: wrap; gap: 6px; }
    .hh-history-values .badge { font-weight: 500; font-size: .74rem; }
    .hh-persona-filter .form-select, .hh-persona-filter .form-control { min-height: 44px; }
    @media(max-width:767.98px) { .hh-ficha-cover { min-height: 95px; } .hh-persona-avatar.lg { width: 85px; height: 85px; flex-basis: 85px; } }
</style>
