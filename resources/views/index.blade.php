<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>My India Business – Make Your Money Work</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', system-ui, -apple-system, sans-serif;
      color: #1a2b5e;
      min-height: 100vh;
      background: #0f172a url('https://thumbs.dreamstime.com/b/abstract-digital-background-glowing-light-blue-green-lines-data-particles-flowing-air-tech-cyberspace-illustration-385631669.jpg') center center / cover no-repeat fixed;
    }

    .hero {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 4rem 5% 6rem;
      position: relative;
      background: linear-gradient(to bottom, rgba(15, 23, 42, 0.65), rgba(15, 23, 42, 0.85));
    }

    .container {
      max-width: 1240px;
      width: 100%;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 5rem;
      align-items: center;
      z-index: 2;
    }

    .left-content {
      padding-right: 2rem;
    }

    .logo {
      font-size: 2.1rem;
      font-weight: 800;
      color: #60a5fa;
      margin-bottom: 1.8rem;
      letter-spacing: -1px;
    }

    h1 {
      font-size: clamp(2.8rem, 6vw, 4.2rem);
      font-weight: 700;
      line-height: 1.12;
      margin-bottom: 1.4rem;
      color: white;
    }

    h1 span {
      color: #93c5fd;
    }

    .subtitle {
      font-size: 1.28rem;
      line-height: 1.65;
      color: #e2e8f0;
      margin-bottom: 2.5rem;
      max-width: 480px;
    }

    .highlight {
      color: #bfdbfe;
      font-weight: 600;
    }

    .buttons {
      display: flex;
      gap: 1.2rem;
      flex-wrap: wrap;
    }

    .btn {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      padding: 0.95rem 2.1rem;
      border-radius: 12px;
      font-size: 1.05rem;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.28s ease;
      box-shadow: 0 4px 14px rgba(0,0,0,0.2);
    }

    .btn-primary {
      background: #3b82f6;
      color: white;
      border: 2px solid #3b82f6;
    }

    .btn-primary:hover {
      background: #2563eb;
      transform: translateY(-3px);
      box-shadow: 0 12px 28px rgba(59,130,246,0.4);
    }

    .btn-outline {
      background: transparent;
      color: white;
      border: 2px solid #93c5fd;
    }

    .btn-outline:hover {
      background: rgba(255,255,255,0.1);
      border-color: #bfdbfe;
      transform: translateY(-2px);
    }

    .store-badges {
      margin-top: 2.2rem;
      display: flex;
      gap: 1.5rem;
      flex-wrap: wrap;
      align-items: center;
    }

    .store-badges img {
      height: 60px;
      width: auto;
      filter: drop-shadow(0 4px 10px rgba(0,0,0,0.3));
      transition: transform 0.25s ease;
    }

    .store-badges img:hover {
      transform: scale(1.08);
    }

    .right-content {
      position: relative;
    }

    .phone-mockup {
      position: relative;
      max-width: 380px;
      margin: 0 auto;
      filter: drop-shadow(0 30px 70px rgba(0,0,0,0.5));
      transform: perspective(1100px) rotateY(-6deg) rotateX(4deg);
      transition: transform 0.5s ease;
    }

    .phone-mockup:hover {
      transform: perspective(1100px) rotateY(-3deg) rotateX(2deg) scale(1.04);
    }

    .phone-mockup img {
      width: 100%;
      height: auto;
      display: block;
      border-radius: 36px;
    }

    @media (max-width: 980px) {
      .container {
        grid-template-columns: 1fr;
        gap: 4rem;
        text-align: center;
      }
      .left-content {
        padding-right: 0;
      }
      h1 {
        font-size: clamp(2.6rem, 7vw, 3.8rem);
      }
      .buttons, .store-badges {
        justify-content: center;
      }
      .phone-mockup {
        max-width: 340px;
        transform: none;
      }
      .phone-mockup:hover {
        transform: scale(1.04);
      }
    }

    @media (max-width: 480px) {
      .hero {
        padding: 3rem 5% 5rem;
      }
      .logo {
        font-size: 1.9rem;
      }
      .btn {
        padding: 0.9rem 1.8rem;
        font-size: 1rem;
      }
      .store-badges img {
        height: 52px;
      }
    }
  </style>
</head>
<body>

  <section class="hero">
    <div class="container">
      <div class="left-content">
        <div class="logo">My India Business</div>
        
        <h1>Get Your Money <span>Working</span></h1>
        
        {{-- <p class="subtitle">
          With impressive interest rates, smart planning tools & easy investment guides — 
          <span class="highlight">My India Business</span> helps you save, grow and secure your future the smartest way.
        </p> --}}

         <p class="subtitle">
         <span class="highlight"> MY INDIA BUSINESS </span>  is a big digital marketing app through which you can get new advertisement every day and through
          My India Business 12 different projects will be started in every village and city of India. 5 crore brothers and sisters will be given employment. Download my India business mobile app today.
        </p>

        <div class="buttons">
          <a href="{{ asset('download/My India Business.apk') }}"
   class="btn btn-primary"
   download>
   Download Now
</a>

          <a href="#" class="btn btn-outline">Learn How It Works →</a>
        </div>

        <div class="store-badges">
          <a href="{{ asset('download/My India Business.apk') }}">
            <img src="https://logos-world.net/wp-content/uploads/2020/12/Google-Play-Logo-2016.png" alt="Get it on Google Play">
          </a>
          <a href="#">
            <img src="https://toppng.com/uploads/preview/app-store-download-on-the-app-store-badge-11760038202udfggvbtov.webp" alt="Download on the App Store">
          </a>
        </div>
      </div>

      <div class="right-content">
        <div class="phone-mockup">
          <!-- Replace with your real app screenshot -->
          <img src="https://www.syncfusion.com/blogs/wp-content/uploads/2021/02/7-Best-Flutter-Charts-for-Visualizing-Income-and-Expenditure.png" alt="My India Business App Screenshot">
        </div>
      </div>
    </div>
  </section>

</body>
</html>