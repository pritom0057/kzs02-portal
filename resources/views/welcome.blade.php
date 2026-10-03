<!doctype html>
<html lang="bn">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>কুষ্টিয়া জিলা স্কুল ব্যাচ-২০০২</title>
<link href="https://fonts.googleapis.com/css2?family=Anek+Bangla:wght@500;600;700;800&family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/kzs.css') }}">
<script src="https://cdn.jsdelivr.net/npm/lucide@0.383.0/dist/umd/lucide.min.js" defer></script>
<script src="{{ asset('js/kzs-lang.js') }}" defer></script>
<script>
(function(){
  var t=localStorage.getItem('kzs_theme')||'light';
  if(t==='dark') document.documentElement.classList.add('dark');
  else document.documentElement.classList.remove('dark');
})();
</script>
</head>
<body>

{{-- ── HEADER ── --}}
<header class="site-header">
  <div class="wrap hdr">
    <a href="{{ route('home') }}" class="brand" aria-label="KZS02">
      <img src="{{ asset('images/kzs02-logo.png') }}" alt="KZS 2002 লোগো" height="40">
      <span class="brand-text">
        <strong data-en="Kushtia Zilla School Batch-2002">কুষ্টিয়া জিলা স্কুল ব্যাচ-২০০২</strong>
      </span>
    </a>
    <div class="lang" role="group" aria-label="Language / ভাষা">
      <button type="button" class="lang-btn" data-lang="bn" aria-pressed="true">বাংলা</button>
      <button type="button" class="lang-btn" data-lang="en" aria-pressed="false">EN</button>
    </div>
    <div class="lang" role="group" aria-label="Theme">
      <button type="button" class="lang-btn" id="themeBtnAuto"  onclick="setTheme('auto')"  aria-pressed="true">🔆</button>
      <button type="button" class="lang-btn" id="themeBtnLight" onclick="setTheme('light')" aria-pressed="false">☀️</button>
      <button type="button" class="lang-btn" id="themeBtnDark"  onclick="setTheme('dark')"  aria-pressed="false">🌙</button>
    </div>
    @auth
      <a href="{{ route('dashboard') }}" class="btn btn-ghost btn-sm" data-en="Member Portal">সদস্য পোর্টাল</a>
    @else
      <a href="{{ route('login') }}" class="btn btn-ghost btn-sm"><i data-lucide="lock" width="15" height="15"></i><span data-en="Member Login">সদস্য লগইন</span></a>
      <a href="{{ route('register') }}" class="btn btn-primary btn-sm" data-en="Join Us">সদস্য হোন</a>
    @endauth
    <button class="icon-btn" id="menuBtn" onclick="toggleMobileMenu()" aria-label="Menu">
      <i data-lucide="menu" width="20" height="20"></i>
    </button>
    <nav class="nav" id="mainNav" aria-label="Main">
      <a href="#home" data-en="Home">হোম</a>
      <a href="#about" data-en="About Us">আমাদের সম্পর্কে</a>
      <a href="#activities" data-en="Activities">কার্যক্রম</a>
      <a href="#gallery" data-en="Gallery">ছবিঘর</a>
      <a href="#organogram" data-en="Organogram">অর্গানোগ্রাম</a>
      <a href="#reunion" data-en="Reunion">রিইউনিয়ন</a>
      <a href="#contact" data-en="Contact">যোগাযোগ</a>
    </nav>
  </div>
</header>

<main>

{{-- ── HERO ── --}}
<section id="home" class="hero">
  <div class="wrap hero-grid">
    <div style="min-width:0">
      <span class="pill" data-en="Proud Batch of 2002">ঐতিহ্যবাহী ব্যাচ ২০০২</span>
      <h1 data-en="Kushtia Zilla School Batch-2002">কুষ্টিয়া জিলা স্কুল ব্যাচ-২০০২</h1>
      <p class="slogan" data-en="Unity, Friendship and Progress">ঐক্য, বন্ধুত্ব ও প্রগতি</p>
      <p class="lead" data-en="Bound by friendship, brotherhood and the pull of childhood — and by the memories of our school, we are all strung on one thread.">বন্ধুত্ব, ভ্রাতৃত্ব ও শৈশবের টানে—শিক্ষাঙ্গনের স্মৃতির টানে আমরা সবাই একসূত্রে গাঁথা।</p>
      <div class="hero-actions">
        @auth
          <a href="{{ route('dashboard') }}" class="btn btn-primary" data-en="Go to Portal">পোর্টালে যান</a>
        @else
          <a href="{{ route('register') }}" class="btn btn-primary" data-en="Register Now">রেজিস্ট্রেশন করুন</a>
          <a href="#organogram" class="btn btn-ghost"><i data-lucide="network" width="18" height="18"></i><span data-en="View Organogram">অর্গানোগ্রাম দেখুন</span></a>
        @endauth
      </div>
      <div class="hero-stats">
        <div class="stat"><b data-en="230+">২৩০+</b><span data-en="Active members">সক্রিয় সদস্য</span></div>
        <div class="stat"><b data-en="25+">২৫+</b><span data-en="Projects and events">প্রজেক্ট ও ইভেন্ট</span></div>
        <div class="stat"><b data-en="20+ yrs">২০+ বছর</b><span data-en="Together">একসাথে পথচলা</span></div>
      </div>
    </div>
    <div class="logo-wrap">
      <div class="logo-plain">
        <img src="{{ asset('images/kzs02-logo.png') }}" alt="KZS 2002 ব্যাচ লোগো">
      </div>
      <span class="chip-float" data-en="Silver Jubilee 2027">সিলভার জুবিলি ২০২৭</span>
    </div>
  </div>
</section>

{{-- ── ABOUT ── --}}
<section id="about" class="section">
  <div class="wrap about">
    <div style="min-width:0">
      <div class="sec-head" style="margin-bottom:24px">
        <span class="eyebrow" data-en="About Us">আমাদের সম্পর্কে</span>
        <h2 data-en="A batch that grew into a family">একটি ব্যাচ, যা হয়ে উঠেছে পরিবার</h2>
      </div>
      <p data-en="We are a non-political, social organization formed by the former students of the Kushtia Zilla School SSC 2002 batch. Holding on to the memories of those golden school days, we have come together today for the welfare of society.">কুষ্টিয়া জিলা স্কুলের এসএসসি ২০০২ ব্যাচের প্রাক্তন ছাত্রদের নিয়ে গঠিত একটি অরাজনৈতিক ও সামাজিক সংগঠন। স্কুলজীবনের সেই সোনালী দিনগুলোর স্মৃতি আঁকড়ে ধরে আজ আমরা সমাজের কল্যাণে একত্রিত হয়েছি।</p>
      <p data-en="Spreading education, supporting underprivileged students, an annual sports festival, and standing beside people in times of social crisis are our core commitments.">শিক্ষা বিস্তার, দরিদ্র শিক্ষার্থীদের সহায়তা, বার্ষিক ক্রীড়া উৎসব এবং বিভিন্ন সামাজিক সংকটে পাশে দাঁড়ানোই আমাদের মূল ব্রত।</p>
      <div class="values">
        <span class="value"><i data-lucide="handshake" width="16" height="16"></i><span data-en="Unity">ঐক্য</span></span>
        <span class="value"><i data-lucide="heart" width="16" height="16"></i><span data-en="Friendship">বন্ধুত্ব</span></span>
        <span class="value"><i data-lucide="trending-up" width="16" height="16"></i><span data-en="Progress">প্রগতি</span></span>
      </div>
    </div>
    <div class="about-card">
      <div class="ico"><i data-lucide="users" width="32" height="32"></i></div>
      <h3 data-en="Walking together for over two decades">একসাথে পথচলা দুই দশকের বেশি</h3>
      <p data-en="From the school grounds to every turn in life, standing beside one another is our greatest strength.">স্কুলের আঙিনা থেকে শুরু করে জীবনের প্রতিটি বাঁকে একে অপরের পাশে থাকা আমাদের মূল শক্তি।</p>
    </div>
  </div>
</section>

{{-- ── ACTIVITIES ── --}}
<section id="activities" class="section section-tint">
  <div class="wrap">
    <div class="sec-head">
      <span class="eyebrow" data-en="What we do">আমরা যা করি</span>
      <h2 data-en="Our Main Activities">আমাদের প্রধান কার্যক্রম</h2>
      <p data-en="Some of our regular initiatives in society and education">সমাজ ও শিক্ষাখাতে আমাদের নিয়মিত কিছু উদ্যোগ</p>
    </div>
    <div class="cards">
      <article class="card">
        <div class="ico"><i data-lucide="book-open" width="26" height="26"></i></div>
        <h3 data-en="Education-Focused Charity">শিক্ষা কেন্দ্রিক দাতব্য কার্যক্রম</h3>
        <p data-en="Covering study costs for poor and talented students, distributing learning materials, and providing various welfare support to school students.">দরিদ্র ও মেধাবী শিক্ষার্থীদের পড়াশোনার খরচ বহন, শিক্ষা উপকরণ বিতরণ এবং স্কুল শিক্ষার্থীদের বিভিন্ন কল্যাণমূলক সহায়তা প্রদান।</p>
      </article>
      <article class="card">
        <div class="ico"><i data-lucide="trophy" width="26" height="26"></i></div>
        <h3 data-en="Annual Cricket Tournament">বার্ষিক ক্রিকেট টুর্নামেন্ট</h3>
        <p data-en="A lively cricket tournament every year with former students from different batches of the school, which strengthens our bond with one another.">স্কুলের বিভিন্ন ব্যাচের প্রাক্তন ছাত্রদের অংশগ্রহণে প্রতিবছর জমজমাট ক্রিকেট টুর্নামেন্টের আয়োজন, যা পারস্পরিক মেলবন্ধন আরও দৃঢ় করে।</p>
      </article>
      <article class="card">
        <div class="ico"><i data-lucide="heart-handshake" width="26" height="26"></i></div>
        <h3 data-en="Social Service">সমাজসেবামূলক কার্যক্রম</h3>
        <p data-en="Humanitarian work such as winter clothes distribution, blood donation drives, relief during natural disasters, and standing beside people in need is carried out regularly.">শীতবস্ত্র বিতরণ, রক্তদান কর্মসূচি, প্রাকৃতিক দুর্যোগে সহায়তা এবং দুঃস্থ মানুষের পাশে দাঁড়ানোর মতো মানবিক কাজ নিয়মিত পরিচালনা করা হয়।</p>
      </article>
    </div>
  </div>
</section>

{{-- ── GALLERY ── --}}
<section id="gallery" class="section">
  <div class="wrap">
    <div class="sec-head">
      <span class="eyebrow" data-en="Gallery">ছবিঘর</span>
      <h2 data-en="Moments from our journey">আমাদের পথচলার কিছু মুহূর্ত</h2>
      <p data-en="Scenes from our work and memories">আমাদের কাজ ও স্মৃতির কিছু চিত্র</p>
    </div>
    <div class="gal">
      <figure class="tile wide">
        <div class="art" style="--c:#fbe4e4" aria-hidden="true">
          <svg viewBox="0 0 600 340" preserveAspectRatio="xMidYMid meet">
            <circle cx="500" cy="78" r="40" fill="#f0b94d"/>
            <ellipse cx="96" cy="92" rx="52" ry="16" fill="#fff" opacity=".75"/>
            <ellipse cx="136" cy="80" rx="34" ry="14" fill="#fff" opacity=".75"/>
            <rect y="268" width="600" height="72" fill="#ebbdbd"/>
            <rect x="120" y="150" width="360" height="118" fill="#c4121a"/>
            <path d="M100 152 L300 80 L500 152Z" fill="#4a0a0d"/>
            <circle cx="300" cy="124" r="14" fill="#fdeeee"/>
            <g fill="#fdeeee"><rect x="140" y="162" width="18" height="100"/><rect x="200" y="162" width="18" height="100"/><rect x="260" y="162" width="18" height="100"/><rect x="322" y="162" width="18" height="100"/><rect x="382" y="162" width="18" height="100"/><rect x="442" y="162" width="18" height="100"/></g>
            <path d="M282 268 V230 a18 18 0 0 1 36 0 V268Z" fill="#f0b94d"/>
            <rect x="104" y="262" width="392" height="12" fill="#7a0d12"/>
            <rect x="298" y="30" width="3" height="52" fill="#58595b"/>
            <path d="M301 32 h34 l-8 10 8 10 h-34Z" fill="#ed1c24"/>
            <circle cx="70" cy="266" r="26" fill="#9bb394"/><circle cx="530" cy="266" r="26" fill="#9bb394"/>
          </svg>
        </div>
        <figcaption><b data-en="The school campus">স্কুলের আঙিনা</b><span data-en="Where our story began">যেখানে আমাদের গল্প শুরু</span></figcaption>
      </figure>
      <figure class="tile">
        <div class="art" style="--c:#f8eee3" aria-hidden="true">
          <svg viewBox="0 0 600 340" preserveAspectRatio="xMidYMid meet">
            <circle cx="470" cy="70" r="34" fill="#f0b94d"/>
            <ellipse cx="300" cy="296" rx="270" ry="56" fill="#b6c9a3"/>
            <rect x="272" y="258" width="56" height="40" rx="4" fill="#e5d6a8"/>
            <g fill="#4a0a0d"><rect x="388" y="190" width="6" height="72" rx="2"/><rect x="404" y="190" width="6" height="72" rx="2"/><rect x="420" y="190" width="6" height="72" rx="2"/></g>
            <g fill="#f0b94d"><rect x="388" y="184" width="22" height="5" rx="2"/><rect x="404" y="184" width="22" height="5" rx="2"/></g>
            <g transform="rotate(-26 220 210)"><rect x="212" y="70" width="16" height="64" rx="6" fill="#4a0a0d"/><rect x="198" y="128" width="44" height="116" rx="14" fill="#e9c58a"/><path d="M220 140 V236" stroke="#c99a52" stroke-width="3"/></g>
            <circle cx="318" cy="236" r="15" fill="#ed1c24"/>
            <path d="M306 230 Q318 238 306 244 M330 230 Q318 238 330 244" stroke="#fff" stroke-width="2" fill="none" stroke-linecap="round"/>
          </svg>
        </div>
        <figcaption><b data-en="On the cricket field">ক্রিকেটের মাঠে</b><span data-en="The annual alumni tournament">প্রাক্তনদের বার্ষিক টুর্নামেন্ট</span></figcaption>
      </figure>
      <figure class="tile">
        <div class="art" style="--c:#fceaea" aria-hidden="true">
          <svg viewBox="0 0 600 340" preserveAspectRatio="xMidYMid meet">
            <circle cx="120" cy="80" r="5" fill="#f0b94d"/><circle cx="486" cy="120" r="7" fill="#f0b94d"/>
            <path d="M300 30 L392 68 L300 106 L208 68Z" fill="#4a0a0d"/>
            <path d="M252 90 V122 C278 140 322 140 348 122 V90 L300 110Z" fill="#7a0d12"/>
            <path d="M386 70 V112" stroke="#f0b94d" stroke-width="4" stroke-linecap="round"/><circle cx="386" cy="116" r="6" fill="#f0b94d"/>
            <g transform="translate(0 44)">
              <path d="M300 250 C252 224 192 224 140 240 V130 C192 114 252 114 300 140Z" fill="#fff" stroke="#c4121a" stroke-width="4" stroke-linejoin="round"/>
              <path d="M300 250 C348 224 408 224 460 240 V130 C408 114 348 114 300 140Z" fill="#fff" stroke="#c4121a" stroke-width="4" stroke-linejoin="round"/>
              <path d="M300 140 V250" stroke="#c4121a" stroke-width="4"/>
            </g>
          </svg>
        </div>
        <figcaption><b data-en="Helping education">শিক্ষায় সহায়তা</b><span data-en="Standing with bright students">মেধাবীদের পাশে</span></figcaption>
      </figure>
      <figure class="tile">
        <div class="art" style="--c:#fde5e5" aria-hidden="true">
          <svg viewBox="0 0 600 340" preserveAspectRatio="xMidYMid meet">
            <circle cx="300" cy="140" r="80" fill="#f8cfd0"/>
            <circle cx="300" cy="120" r="44" fill="#e0a0a0"/>
            <path d="M200 240 Q300 200 400 240 L420 340 H180Z" fill="#c4121a"/>
            <circle cx="268" cy="108" r="12" fill="#fff" opacity=".6"/>
            <circle cx="332" cy="108" r="12" fill="#fff" opacity=".6"/>
          </svg>
        </div>
        <figcaption><b data-en="Together always">সর্বদা একসাথে</b><span data-en="Brothers in every season">প্রতিটি মৌসুমে ভাই</span></figcaption>
      </figure>
      <figure class="tile">
        <div class="art" style="--c:#f4e7e7" aria-hidden="true">
          <svg viewBox="0 0 600 340" preserveAspectRatio="xMidYMid meet">
            <rect x="60" y="80" width="480" height="200" rx="20" fill="#f8cfd0"/>
            <rect x="90" y="110" width="180" height="140" rx="12" fill="#c4121a" opacity=".8"/>
            <rect x="330" y="110" width="180" height="140" rx="12" fill="#7a0d12" opacity=".8"/>
            <circle cx="180" cy="180" r="30" fill="#fff" opacity=".3"/>
            <circle cx="420" cy="180" r="30" fill="#fff" opacity=".3"/>
          </svg>
        </div>
        <figcaption><b data-en="Social service">সমাজসেবা</b><span data-en="Giving back to the community">সমাজের কাছে ফেরত দেওয়া</span></figcaption>
      </figure>
      <figure class="tile">
        <div class="art" style="--c:#fbe4e4" aria-hidden="true">
          <svg viewBox="0 0 600 340" preserveAspectRatio="xMidYMid meet">
            <ellipse cx="300" cy="300" rx="240" ry="40" fill="#f0b0b0"/>
            <rect x="220" y="100" width="160" height="200" rx="16" fill="#7a0d12"/>
            <rect x="240" y="80" width="120" height="40" rx="8" fill="#c4121a"/>
            <rect x="252" y="140" width="40" height="60" rx="8" fill="#fdeeee"/>
            <rect x="308" y="140" width="40" height="60" rx="8" fill="#fdeeee"/>
            <rect x="252" y="220" width="96" height="80" rx="8" fill="#f0b94d"/>
          </svg>
        </div>
        <figcaption><b data-en="Our meeting hall">আমাদের মিলনক্ষেত্র</b><span data-en="Where memories are made">যেখানে স্মৃতি তৈরি হয়</span></figcaption>
      </figure>
    </div>
  </div>
</section>

{{-- ── ORGANOGRAM ── --}}
<section id="organogram" class="section section-tint">
  <div class="wrap">
    <div class="sec-head">
      <span class="eyebrow" data-en="Organogram">অর্গানোগ্রাম</span>
      <h2 data-en="How our organization is structured">আমাদের সংগঠনের কাঠামো</h2>
      <p data-en="From the advisory council to every member, this is how responsibility flows.">উপদেষ্টা পরিষদ থেকে প্রতিটি সদস্য পর্যন্ত দায়িত্বের ধারা।</p>
    </div>
    <div class="org-card">
      <div class="org">
        <div class="org-row"><div class="node n0"><b data-en="Advisory Council">উপদেষ্টা পরিষদ</b><span data-en="Name to be added">নাম যুক্ত হবে</span></div></div>
        <div class="org-line"></div>
        <div class="org-row">
          <div class="node n1"><b data-en="President">সভাপতি</b><span data-en="Name to be added">নাম যুক্ত হবে</span></div>
          <div class="node n1b"><b data-en="Vice President">সহ-সভাপতি</b><span data-en="Name to be added">নাম যুক্ত হবে</span></div>
        </div>
        <div class="org-line"></div>
        <div class="org-row">
          <div class="node n2"><b data-en="General Secretary">সাধারণ সম্পাদক</b><span data-en="Name to be added">নাম যুক্ত হবে</span></div>
          <div class="node n2"><b data-en="Organizing Secretary">সাংগঠনিক সম্পাদক</b><span data-en="Name to be added">নাম যুক্ত হবে</span></div>
          <div class="node n2"><b data-en="Treasurer">কোষাধ্যক্ষ</b><span data-en="Name to be added">নাম যুক্ত হবে</span></div>
        </div>
        <div class="org-line"></div>
        <div class="org-label" data-en="Department committees">বিভাগীয় কমিটি</div>
        <div class="depts">
          <div class="node"><b data-en="Education &amp; Charity">শিক্ষা ও দাতব্য</b><span data-en="Department secretary">বিভাগীয় সম্পাদক</span></div>
          <div class="node"><b data-en="Sports">ক্রীড়া</b><span data-en="Department secretary">বিভাগীয় সম্পাদক</span></div>
          <div class="node"><b data-en="Social Service &amp; Blood Donation">সমাজসেবা ও রক্তদান</b><span data-en="Department secretary">বিভাগীয় সম্পাদক</span></div>
          <div class="node"><b data-en="Culture &amp; Reunion">সাংস্কৃতিক ও রিইউনিয়ন</b><span data-en="Department secretary">বিভাগীয় সম্পাদক</span></div>
          <div class="node"><b data-en="Publicity &amp; IT">প্রচার ও আইটি</b><span data-en="Department secretary">বিভাগীয় সম্পাদক</span></div>
        </div>
        <div class="org-line"></div>
        <div class="org-row"><div class="node n4"><b data-en="All Members">সকল সদস্য</b><span data-en="150+ batchmates">১৫০+ ব্যাচমেট</span></div></div>
      </div>
    </div>
  </div>
</section>

{{-- ── MEMBER TEASER ── --}}
<section class="section" style="padding-block:28px 0">
  <div class="wrap">
    <div class="teaser">
      <div class="ico"><i data-lucide="users-round" width="30" height="30" color="#fff"></i></div>
      <div style="min-width:0">
        <h2 data-en="Members have a space of their own">সদস্যদের জন্য আলাদা এলাকা</h2>
        <p data-en="Batchmates can sign in to share on the batch wall and keep their own profile.">ব্যাচের বন্ধুরা লগইন করে ওয়ালে লিখতে ও নিজের প্রোফাইল সাজাতে পারবেন।</p>
      </div>
      @auth
        <a href="{{ route('dashboard') }}" class="btn btn-light" data-en="Go to Portal">পোর্টালে যান</a>
      @else
        <a href="{{ route('login') }}" class="btn btn-light" data-en="Member Login">সদস্য লগইন</a>
      @endauth
    </div>
  </div>
</section>

{{-- ── REUNION ── --}}
<section id="reunion" class="section">
  <div class="wrap">
    <div class="reunion">
      <figure class="shield">
        <img src="{{ asset('images/reunion-logo.png') }}" alt="Silver Jubilee — Batch'02 Kushtia Zilla School, Celebrating Unity, Remembering Beginnings">
      </figure>
      <div style="min-width:0">
        <span class="pill" data-en="Special Announcement">বিশেষ ঘোষণা</span>
        <h2 data-en="Upcoming Silver Jubilee &amp; Annual Reunion 2027">আসন্ন সিলভার জুবিলি ও বার্ষিক পুনর্মিলনী ২০২৭</h2>
        <p data-en="Preparations are underway for a grand celebration marking 25 years of Kushtia Zilla School Batch-2002. Stay connected for registration and details.">কুষ্টিয়া জিলা স্কুল ব্যাচ-২০০২ এর ২৫ বছর পূর্তি উপলক্ষে জমকালো আয়োজনের প্রস্তুতি চলছে। রেজিস্ট্রেশন ও বিস্তারিত তথ্যের জন্য যুক্ত থাকুন।</p>
        @auth
          <a href="{{ route('event.show') }}" class="btn btn-primary" data-en="Event Registration">ইভেন্ট রেজিস্ট্রেশন</a>
        @else
          <a href="{{ route('login') }}" class="btn btn-primary" data-en="Event Registration">ইভেন্ট রেজিস্ট্রেশন</a>
        @endauth
      </div>
    </div>
  </div>
</section>

</main>

{{-- ── FOOTER ── --}}
<footer id="contact" class="site-footer">
  <div class="wrap">
    <div class="foot">
      <div>
        <span class="foot-logo"><img src="{{ asset('images/kzs02-logo.png') }}" alt="KZS 2002" height="34"></span>
        <p class="foot-slogan" data-en="Unity, Friendship and Progress">ঐক্য, বন্ধুত্ব ও প্রগতি</p>
        <p data-en="A non-political social welfare organization of the former students of the Kushtia Zilla School SSC 2002 batch.">কুষ্টিয়া জিলা স্কুল এসএসসি ২০০২ ব্যাচের প্রাক্তন ছাত্রদের একটি অরাজনৈতিক ও সামাজিক কল্যাণমূলক সংগঠন।</p>
      </div>
      <div>
        <h4 data-en="Quick Links">দ্রুত লিংক</h4>
        <ul>
          <li><a href="#about" data-en="About Us">আমাদের সম্পর্কে</a></li>
          <li><a href="#activities" data-en="Activities">কার্যক্রম</a></li>
          <li><a href="#gallery" data-en="Gallery">ছবিঘর</a></li>
          <li><a href="#organogram" data-en="Organogram">অর্গানোগ্রাম</a></li>
          <li><a href="#reunion" data-en="Reunion">রিইউনিয়ন</a></li>
          <li><a href="{{ route('register') }}" data-en="Member Registration">সদস্য রেজিস্ট্রেশন</a></li>
          <li><a href="{{ route('login') }}" data-en="Member Login">সদস্য লগইন</a></li>
        </ul>
      </div>
      <div>
        <h4 data-en="Contact">যোগাযোগ</h4>
        <p class="cline"><i data-lucide="map-pin" width="16" height="16"></i><span data-en="Kushtia Zilla School, Kushtia, Bangladesh">কুষ্টিয়া জিলা স্কুল, কুষ্টিয়া, বাংলাদেশ</span></p>
        <p class="cline"><i data-lucide="mail" width="16" height="16"></i><span>admin@kzs02.com</span></p>
        <p class="cline"><i data-lucide="phone" width="16" height="16"></i><span>+880 1717-058286</span></p>
      </div>
    </div>
    <p class="copy" data-en="&copy; {{ date('Y') }} Kushtia Zilla School Batch-2002. All rights reserved.">&copy; {{ date('Y') }} কুষ্টিয়া জিলা স্কুল ব্যাচ-২০০২। সর্বস্বত্ব সংরক্ষিত।</p>
  </div>
</footer>

<script>
document.addEventListener('DOMContentLoaded', function () {
  if (window.lucide) lucide.createIcons();
});
function toggleMobileMenu(){var n=document.getElementById('mainNav');if(n)n.classList.toggle('mob-open');}
document.addEventListener('click',function(e){var n=document.getElementById('mainNav'),b=document.getElementById('menuBtn');if(n&&b&&!n.contains(e.target)&&!b.contains(e.target))n.classList.remove('mob-open');});
function _getTheme(){return localStorage.getItem('kzs_theme')||'light';}
function _applyTheme(t){
  var isDark=t==='dark'||(t==='auto'&&window.matchMedia('(prefers-color-scheme:dark)').matches);
  isDark?document.documentElement.classList.add('dark'):document.documentElement.classList.remove('dark');
  var ba=document.getElementById('themeBtnAuto'),bl=document.getElementById('themeBtnLight'),bd=document.getElementById('themeBtnDark');
  if(ba)ba.setAttribute('aria-pressed',t==='auto'?'true':'false');
  if(bl)bl.setAttribute('aria-pressed',t==='light'?'true':'false');
  if(bd)bd.setAttribute('aria-pressed',t==='dark'?'true':'false');
}
function setTheme(t){
  localStorage.setItem('kzs_theme', t);
  _applyTheme(t);
}
(function(){_applyTheme(_getTheme());})();
document.addEventListener('DOMContentLoaded', function(){_applyTheme(_getTheme());});
</script>
</body>
</html>
