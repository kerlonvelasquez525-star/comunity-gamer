<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  @include('partials.head', ['title' => 'Nova Community'])
  <link rel="stylesheet" href="{{ asset('css/style_th.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>

  <!-- HEADER NAVEGACIÓN -->
  <header>
    <div class="brand">
      <div class="brand-logo">GV</div>
      <div class="brand-title">nova-comunity</div>
    </div>
    <nav>
      <a href="{{ route('home') }}" class="active">Home</a>
      <a href="#">noticias</a>
      <a href="#">comentarios</a>
      <a href="#">comunidad</a>
      <a href="#">quejas</a>
    </nav>
    <div class="header-right">
      <div class="sys-status">
        <span class="status-dot"></span>
        SYS_STATUS: ONLINE
      </div>
      <a href="{{ route('login') }}" class="btn-signin"><i class="fa-solid fa-user"></i> &gt;</a>
    </div>
  </header>

  <!-- SECCIÓN HERO PRINCIPAL -->
  <main class="hero-container">
    
    <!-- COLUMNA IZQUIERDA -->
    <section class="hero-left">
      <div class="badge-tag">BIENVENIDO // NUEVOS JUEGOS CADA DIA</div>
      <h1 class="hero-title">CONOCE TUS JUEGOS</h1>
      <p class="hero-description">
        Reseñas tácticas, datos de esports en bruto y publicaciones de la comunidad sin filtros. GameVault es tu banco de memoria externo para conocer tus juegos.
      </p>

      <div class="cta-group">
        <a href="{{ route('login') }}" class="btn-primary">iniciar sesion &gt;</a>
        <a href="{{ route('register') }}" class="btn-icon">registrarse &gt;</a>
      </div>

      <!-- Métricas / Estadísticas -->
      <div class="stats-card">
        <div class="stat-item">
          <h3>248K</h3>
          <p>USUARIOS ACTIVOS</p>
        </div>
        <div class="stat-item">
          <h3>12.4M</h3>
          <p>POST DIARIOS</p>
        </div>
        <div class="stat-item">
          <h3>98.4%</h3>
          <p>TELERIMETRIA</p>
        </div>
      </div>

      <!-- COMPONENTE IZQUIERDA ABAJO: SALAS DE DESPLIEGUE Y VOZ -->
      <div class="extra-section">
        <div class="extra-header">
          <span class="extra-title">⚡ SALAS DE CONVERSACION ACTIVAS</span>
          <span class="voice-tag">FILTRAR POR JUEGO</span>
        </div>
        <div class="squad-list">
          <div class="squad-item">
            <div class="squad-info">
              <h4>BUGS NUEVOS</h4>
              <p>Cyberpunk / Extraction •</p>
            </div>
            <button class="btn-join">UNIRSE</button>
          </div>
          <div class="squad-item">
            <div class="squad-info">
              <h4>BUSCANDO SQUADS</h4>
              <p>FORTNITE, CSGO, COD • 4-5 Jugadores</p>
            </div>
            <button class="btn-join">UNIRSE</button>
          </div>
        </div>
      </div>

    </section>

    <!-- COLUMNA DERECHA: BARRITA DESLIZADORA DE JUEGOS -->
    <section class="hero-right">
      <div class="slider-header">
        <span class="slider-title">EXPLORAR BASES DE DATOS</span>
        <div class="slider-controls">
          <button class="control-btn" id="slider-up" aria-label="Subir"><i class="fa-solid fa-chevron-up"></i></button>
          <button class="control-btn" id="slider-down" aria-label="Bajar"><i class="fa-solid fa-chevron-down"></i></button>
        </div>
      </div>

      <div class="games-slider" id="slider">
        
        <!-- CAJA DE JUEGO 1 -->
        <article class="game-card">
          <img src="https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=800&q=80" alt="Proyecto Blackout" class="game-image">
          <div class="game-card-body">
            <div>
              <span class="news-badge">ÚLTIMAS NOTICIAS</span>
              <span class="read-time">4 MIN READ</span>
            </div>
            <h2 class="game-card-title">PROYECTO BLACKOUT: Descifrando las nuevas reglas de extracción en los videojuegos</h2>
            <p class="game-card-desc">
              Un análisis exhaustivo de las mecánicas del parche v4.12, las nubes de radiación dinámicas y las rutas tácticas óptimas de despliegue.
            </p>
            <div class="game-card-footer">
              <div class="operator-info">
                <div class="avatar-sm"></div>
                <span class="operator-name">GhostOperator</span>
              </div>
              <span class="deploy-time">PUBLICADO: HACE 2H</span>
            </div>
          </div>
        </article>

        <!-- CAJA DE JUEGO 2 -->
        <article class="game-card">
          <img src="https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&w=800&q=80" alt="Neon Velocity" class="game-image">
          <div class="game-card-body">
            <div>
              <span class="news-badge">ULTIMOS PARCHES</span>
              <span class="read-time">6 MIN READ</span>
            </div>
            <h2 class="game-card-title">NEON VELOCITY: Actualización de motor de aceleración</h2>
            <p class="game-card-desc">
              Revisión completa de la física de derrape en circuitos urbanos y nuevo soporte para enlaces cibernéticos nivel 3.
            </p>
            <div class="game-card-footer">
              <div class="operator-info">
                <div class="avatar-sm"></div>
                <span class="operator-name">ViperNet</span>
              </div>
              <span class="deploy-time">PUBLICADO: HACE 5H</span>
            </div>
          </div>
        </article>

        <!-- CAJA DE JUEGO 3 -->
        <article class="game-card">
          <img src="https://images.unsplash.com/photo-1538481199705-c710c4e965fc?auto=format&fit=crop&w=800&q=80" alt="Cyber Protocol" class="game-image">
          <div class="game-card-body">
            <div>
              <span class="news-badge">FINAL DEL TORNEO</span>
              <span class="read-time">3 MIN READ</span>
            </div>
            <h2 class="game-card-title">CYBER PROTOCOL: Clasificatorias globales de la temporada 5</h2>
            <p class="game-card-desc">
              Conoce los mejores equipos de la división Cyber, estadísticas de selecciones y fechas para las finales.
            </p>
            <div class="game-card-footer">
              <div class="operator-info">
                <div class="avatar-sm"></div>
                <span class="operator-name">NovaAdmin</span>
              </div>
              <span class="deploy-time">PUBLICADO: HACE 1D</span>
            </div>
          </div>
        </article>

        <!-- CAJA DE JUEGO 4 -->
        <article class="game-card">
          <img src="https://i0.wp.com/www.gamerfocus.co/wp-content/uploads/2026/08/fortnite_og_evento_temporada_9_mecha_monstruo.jpg?resize=860%2C484&ssl=1" alt="Proyecto Blackout" class="game-image">
          <div class="game-card-body">
            <div>
              <span class="news-badge">FORTNITE</span>
              <span class="read-time">4 MIN READ</span>
            </div>
            <h2 class="game-card-title">Fecha, hora y cómo participar en el evento de historia de Fortnite OG: El enfrentamiento «final» del mecha contra el monstruo</h2>
            <p class="game-card-desc">
              ¿Cambiará el ganador esta vez?
            </p>
            <div class="game-card-footer">
              <div class="operator-info">
                <div class="avatar-sm"></div>
                <span class="operator-name">GhostOperator</span>
              </div>
              <span class="deploy-time">PUBLICADO: HACE 2H</span>
            </div>
          </div>
        </article>
        <!-- CAJA DE JUEGO 5 -->
        <article class="game-card">
          <img src="https://i0.wp.com/paranoiasgamersweb.com/wp-content/uploads/2026/06/Elden-Ring-Nintendo-Switch-2.jpg?resize=1536%2C864&ssl=1" alt="Proyecto Blackout" class="game-image">
          <div class="game-card-body">
            <div>
              <span class="news-badge">ELDEN RING</span>
              <span class="read-time">8 MIN READ</span>
            </div>
            <h2 class="game-card-title">¿Por qué Elden Ring es un fenómeno y por qué deberíamos estar muy atentos a su estreno en Nintendo Switch 2?</h2>
            <p class="game-card-desc">
              El juego de FromSoftware se prepara para su estreno en Nintendo Switch 2 con ELDEN RING Tarnished Edition el próximo 28 de agosto, y aunque a estas alturas conocemos perfectamente el juego que tenemos entre manos, su llegada a la consola de Nintendo es mucho más importante de lo que parece.
            </p>
            <div class="game-card-footer">
              <div class="operator-info">
                <div class="avatar-sm"></div>
                <span class="operator-name">KILLER69</span>
              </div>
              <span class="deploy-time">PUBLICADO: HACE 3H</span>
            </div>
          </div>
        </article>
        <!-- CAJA DE JUEGO 6 -->
        <article class="game-card">
          <img src="https://img.asmedia.epimg.net/resizer/v2/V6OMABBBSZFCLKC3JPM33BIS3E.jpg?auth=f9cc206a3af28441fa0d37420d949560a0486a999beee122d8adc96917b1ea85&width=956&height=538&smart=true" alt="Proyecto Blackout" class="game-image">
          <div class="game-card-body">
            <div>
              <span class="news-badge">MINECRAFT</span>
              <span class="read-time">4 MIN READ</span>
            </div>
            <h2 class="game-card-title">Minecraft incluirá en su próxima actualización una de las funciones más esperadas: los usuarios llevan pidiéndola más de 15 años</h2>
            <p class="game-card-desc">
              Un análisis exhaustivo de las mecánicas del parche v4.12, las nubes de radiación dinámicas y las rutas tácticas óptimas de despliegue.
            </p>
            <div class="game-card-footer">
              <div class="operator-info">
                <div class="avatar-sm"></div>
                <span class="operator-name">PINCHO_XX</span>
              </div>
              <span class="deploy-time">PUBLICADO: HACE 2D</span>
            </div>
          </div>
        </article>

      </div>
    </section>

  </main>

  <script>
    const resetWelcomePosition = () => {
      window.scrollTo({ top: 0, left: 0, behavior: 'instant' });

      const slider = document.getElementById('slider');
      const btnUp = document.getElementById('slider-up');
      const btnDown = document.getElementById('slider-down');

      if (slider) {
        slider.scrollTo({ top: 0, left: 0, behavior: 'instant' });
      }

      if (slider && btnUp && btnDown) {
        const scrollAmount = () => slider.clientHeight * 0.9;

        btnUp.addEventListener('click', () => {
          slider.scrollBy({ top: -scrollAmount(), behavior: 'smooth' });
        });

        btnDown.addEventListener('click', () => {
          slider.scrollBy({ top: scrollAmount(), behavior: 'smooth' });
        });
      }
    };

    document.addEventListener('DOMContentLoaded', resetWelcomePosition);
    document.addEventListener('livewire:navigated', resetWelcomePosition);
  </script>

</body>
</html>