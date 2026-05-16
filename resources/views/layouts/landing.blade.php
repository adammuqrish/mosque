<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Smart Donation and Volunteer Engagement Platform for Al-Mukminun Mosque">
    <title>@yield('title', 'Al-Mukminun Mosque — Smart Donation & Volunteer Platform')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <!-- Application CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <style>
        [x-cloak] { display: none !important; }

        /* Landing-specific typography */
        .font-heading { font-family: 'Inter', system-ui, sans-serif; }
        .font-body { font-family: 'Inter', system-ui, sans-serif; }
        .font-arabic { font-family: 'Amiri', serif; }

        /* Smooth scroll behavior */
        html { scroll-behavior: smooth; }

        /* Custom animations */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(24px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes pulse-soft {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        .animate-fadeInUp { animation: fadeInUp 0.7s ease-out forwards; }
        .animate-fadeIn { animation: fadeIn 0.5s ease-out forwards; }
        .animate-slideDown { animation: slideDown 0.5s ease-out forwards; }
        .animate-pulse-soft { animation: pulse-soft 3s ease-in-out infinite; }

        /* Staggered animation delays */
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; }
        .delay-500 { animation-delay: 0.5s; }

        /* Hero gradient overlay */
        .hero-gradient {
            background: linear-gradient(135deg, #0B6E4F 0%, #084B3B 50%, #1B2A4A 100%);
        }

        /* Card hover lift */
        .card-lift {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card-lift:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
        }

        /* Icon scale on hover */
        .icon-scale {
            transition: transform 0.3s ease;
        }
        .card-lift:hover .icon-scale {
            transform: scale(1.1);
        }

        /* Connector line between steps */
        .step-connector {
            position: absolute;
            top: 24px;
            left: calc(50% + 32px);
            right: calc(-50% + 32px);
            height: 2px;
            background: linear-gradient(90deg, #C5A059, #0B6E4F);
            z-index: 0;
        }

        @media (max-width: 768px) {
            .step-connector { display: none; }
        }

        /* Mobile menu animation */
        .mobile-menu-enter {
            animation: slideDown 0.3s ease-out forwards;
        }

        /* Progress bar animation */
        .progress-bar {
            transition: width 1.5s ease-out;
        }

        /* Gradient text */
        .gradient-text {
            background: linear-gradient(135deg, #0B6E4F, #C5A059);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Subtle noise texture for hero */
        .hero-texture {
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.03'/%3E%3C/svg%3E");
        }
    </style>
</head>

<body class="bg-[#FAFAF5] font-body text-[#1A1A2E] antialiased">

    @yield('content')

    <!-- Minimal Alpine.js for mobile menu -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <!-- Alpine.js: Join Event AJAX handler + Toast notifications -->
    <script>
        function joinEvent(eventId, alreadyJoined) {
            return {
                joined: alreadyJoined,
                loading: false,
                async submit(e) {
                    this.loading = true;
                    const form = e.target;
                    const csrfToken = form.querySelector('input[name="_token"]').value;

                    try {
                        const response = await fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            },
                            body: new FormData(form),
                        });
                        const data = await response.json();

                        if (data.success) {
                            this.joined = true;
                            showToast(data.message, 'success');
                        } else {
                            showToast(data.message, 'error');
                        }
                    } catch (err) {
                        showToast('Something went wrong. Please try again.', 'error');
                    } finally {
                        this.loading = false;
                    }
                }
            };
        }

        function showToast(message, type) {
            const toast = document.createElement('div');
            const colors = {
                success: 'bg-[#0B6E4F] text-white',
                error: 'bg-red-600 text-white',
            };
            toast.className = `fixed bottom-6 right-6 ${colors[type]} px-5 py-3 rounded-lg shadow-lg z-50 text-sm font-medium transition-all duration-300 opacity-0 transform translate-y-2`;
            toast.textContent = message;
            document.body.appendChild(toast);

            requestAnimationFrame(() => {
                toast.classList.remove('opacity-0', 'translate-y-2');
            });

            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    </script>

    <!-- Simple intersection observer for scroll animations -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

            document.querySelectorAll('.animate-on-scroll').forEach(function(el) {
                el.style.opacity = '0';
                el.style.transform = 'translateY(24px)';
                el.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out';
                observer.observe(el);
            });
        });
    </script>

    @yield('scripts')
</body>

</html>
