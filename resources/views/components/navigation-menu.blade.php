<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">
                <!-- Logo Compacto -->
                <div class="sb-sidenav-header">
                    <div class="d-flex align-items-center">
                        <div class="sb-sidenav-logo-icon">
                            <i class="fas fa-store"></i>
                        </div>
                        <div class="ms-2">
                            <div class="fw-bold" style="font-size: 0.9rem;">Sistema</div>
                            <small class="text-muted" style="font-size: 0.7rem;">Admin</small>
                        </div>
                    </div>
                </div>

                <div class="sb-sidenav-menu-heading" style="padding: 0.5rem 1rem 0.3rem 1rem; font-size: 0.7rem;">Inicio
                </div>
                @if (auth()->user()->email !== 'invitado@gmail.com')
                    <a class="nav-link" href="{{ route('panel') }}"
                        style="padding: 0.6rem 0.8rem; margin: 0.1rem 0.3rem;">
                        <div class="sb-nav-link-icon" style="font-size: 0.9rem;"><i class="fas fa-tachometer-alt"></i>
                        </div>
                        Panel
                        <span class="sb-nav-link-badge" style="font-size: 0.6rem; padding: 0.1rem 0.3rem;">Home</span>
                    </a>
                @endif

                <div class="sb-sidenav-menu-heading" style="padding: 0.5rem 1rem 0.3rem 1rem; font-size: 0.7rem;">
                    Modulos</div>

                @can('ver-producto')
                    <a class="nav-link" href="{{ route('productos.index') }}"
                        style="padding: 0.6rem 0.8rem; margin: 0.1rem 0.3rem;">
                        <div class="sb-nav-link-icon" style="font-size: 0.9rem;"><i class="fa-brands fa-shopify"></i></div>
                        Productos
                        <span class="sb-nav-link-badge" style="font-size: 0.6rem; padding: 0.1rem 0.3rem;">Inv</span>
                    </a>
                @endcan

                <!----Compras---->
                @can('ver-compra')
                    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseCompras"
                        aria-expanded="false" aria-controls="collapseLayouts"
                        style="padding: 0.6rem 0.8rem; margin: 0.1rem 0.3rem;">
                        <div class="sb-nav-link-icon" style="font-size: 0.9rem;"><i class="fa-solid fa-store"></i></div>
                        Compras
                        <div class="sb-sidenav-collapse-arrow" style="font-size: 0.8rem;"><i
                                class="fas fa-chevron-down"></i></div>
                        <span class="sb-nav-link-badge" style="font-size: 0.6rem; padding: 0.1rem 0.3rem;">Proc</span>
                    </a>
                    <div class="collapse" id="collapseCompras" aria-labelledby="headingOne"
                        data-bs-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav" style="padding-left: 1rem;">
                            @can('ver-compra')
                                <a class="nav-link" href="{{ route('compras.index') }}"
                                    style="padding: 0.5rem 0.8rem; font-size: 0.85rem;">
                                    <i class="fas fa-list me-1" style="font-size: 0.8rem;"></i>Lista
                                </a>
                            @endcan
                            @can('crear-compra')
                                <a class="nav-link" href="{{ route('compras.create') }}"
                                    style="padding: 0.5rem 0.8rem; font-size: 0.85rem;">
                                    <i class="fas fa-plus me-1" style="font-size: 0.8rem;"></i>Crear
                                </a>
                            @endcan
                        </nav>
                    </div>
                @endcan

                <!----Ventas---->
                @can('ver-venta')
                    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseVentas"
                        aria-expanded="false" aria-controls="collapseLayouts"
                        style="padding: 0.6rem 0.8rem; margin: 0.1rem 0.3rem;">
                        <div class="sb-nav-link-icon" style="font-size: 0.9rem;"><i class="fa-solid fa-cart-shopping"></i>
                        </div>
                        Ventas
                        <div class="sb-sidenav-collapse-arrow" style="font-size: 0.8rem;"><i
                                class="fas fa-chevron-down"></i></div>
                        <span class="sb-nav-link-badge" style="font-size: 0.6rem; padding: 0.1rem 0.3rem;">Sales</span>
                    </a>
                    <div class="collapse" id="collapseVentas" aria-labelledby="headingOne"
                        data-bs-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav" style="padding-left: 1rem;">
                            @can('ver-venta')
                                <a class="nav-link" href="{{ route('ventas.index') }}"
                                    style="padding: 0.5rem 0.8rem; font-size: 0.85rem;">
                                    <i class="fas fa-list me-1" style="font-size: 0.8rem;"></i>Lista
                                </a>
                            @endcan
                            @can('crear-venta')
                                <a class="nav-link" href="{{ route('ventas.create') }}"
                                    style="padding: 0.5rem 0.8rem; font-size: 0.85rem;">
                                    <i class="fas fa-plus me-1" style="font-size: 0.8rem;"></i>Crear
                                </a>
                            @endcan
                        </nav>
                    </div>
                @endcan

                @can('ver-categoria')
                    <a class="nav-link" href="{{ route('categorias.index') }}"
                        style="padding: 0.6rem 0.8rem; margin: 0.1rem 0.3rem;">
                        <div class="sb-nav-link-icon" style="font-size: 0.9rem;"><i class="fa-solid fa-tags"></i></div>
                        Categorias
                        <span class="sb-nav-link-badge" style="font-size: 0.6rem; padding: 0.1rem 0.3rem;">Cat</span>
                    </a>
                @endcan

                @can('ver-tipounidad')
                    <a class="nav-link" href="{{ route('tipounidades.index') }}"
                        style="padding: 0.6rem 0.8rem; margin: 0.1rem 0.3rem;">
                        <div class="sb-nav-link-icon" style="font-size: 0.9rem;"><i class="fa-solid fa-tags"></i></div>
                        Tipos de Unidades
                        <span class="sb-nav-link-badge" style="font-size: 0.6rem; padding: 0.1rem 0.3rem;">Unit</span>
                    </a>
                @endcan


                @can('ver-marca')
                    <a class="nav-link" href="{{ route('marcas.index') }}"
                        style="padding: 0.6rem 0.8rem; margin: 0.1rem 0.3rem;">
                        <div class="sb-nav-link-icon" style="font-size: 0.9rem;"><i class="fa-solid fa-certificate"></i>
                        </div>
                        Marcas
                        <span class="sb-nav-link-badge" style="font-size: 0.6rem; padding: 0.1rem 0.3rem;">Brand</span>
                    </a>
                @endcan

                @can('ver-cliente')
                    <a class="nav-link" href="{{ route('clientes.index') }}"
                        style="padding: 0.6rem 0.8rem; margin: 0.1rem 0.3rem;">
                        <div class="sb-nav-link-icon" style="font-size: 0.9rem;"><i class="fa-solid fa-users"></i></div>
                        Clientes
                        <span class="sb-nav-link-badge" style="font-size: 0.6rem; padding: 0.1rem 0.3rem;">Cli</span>
                    </a>
                @endcan

                @can('ver-proveedor')
                    <a class="nav-link" href="{{ route('proveedores.index') }}"
                    style="padding: 0.6rem 0.8rem; margin: 0.1rem 0.3rem;">
                        <div class="sb-nav-link-icon" style="font-size: 0.9rem;"><i class="fa-solid fa-truck"></i></div>
                        Proveedores
                        <span class="sb-nav-link-badge" style="font-size: 0.6rem; padding: 0.1rem 0.3rem;">Prov</span>
                    </a>
                @endcan


                @can('ver-almacen')
                    <a class="nav-link" href="{{ route('almacenes.index') }}"
                        style="padding: 0.6rem 0.8rem; margin: 0.1rem 0.3rem;">
                        <div class="sb-nav-link-icon" style="font-size: 0.9rem;"><i class="fa-solid fa-tags"></i></div>
                        Almacenes
                        <span class="sb-nav-link-badge" style="font-size: 0.6rem; padding: 0.1rem 0.3rem;">Alm</span>
                    </a>
                @endcan

                @hasrole('administrador')
                    <div class="sb-sidenav-menu-heading" style="padding: 0.5rem 1rem 0.3rem 1rem; font-size: 0.7rem;">
                        Administración</div>
                @endhasrole

                @can('ver-user')
                    <a class="nav-link" href="{{ route('users.index') }}"
                        style="padding: 0.6rem 0.8rem; margin: 0.1rem 0.3rem;">
                        <div class="sb-nav-link-icon" style="font-size: 0.9rem;"><i class="fa-solid fa-user-gear"></i>
                        </div>
                        Usuarios
                        <span class="sb-nav-link-badge" style="font-size: 0.6rem; padding: 0.1rem 0.3rem;">Users</span>
                    </a>
                @endcan

                @can('ver-role')
                    <a class="nav-link" href="{{ route('roles.index') }}"
                        style="padding: 0.6rem 0.8rem; margin: 0.1rem 0.3rem;">
                        <div class="sb-nav-link-icon" style="font-size: 0.9rem;"><i class="fa-solid fa-shield-alt"></i>
                        </div>
                        Roles
                        <span class="sb-nav-link-badge" style="font-size: 0.6rem; padding: 0.1rem 0.3rem;">Roles</span>
                    </a>
                @endcan

            </div>
        </div>
        <div class="sb-sidenav-footer" style="padding: 0.8rem;">
            <div class="small" style="font-size: 0.7rem;">Conectado:</div>
            <div class="d-flex align-items-center">
                <div class="user-avatar" style="font-size: 1.2rem;">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="ms-2">
                    <strong style="font-size: 0.8rem;">{{ auth()->user()->name }}</strong>
                    <div class="small text-muted" style="font-size: 0.65rem;">
                        {{ auth()->user()->getRoleNames()->first() }}</div>
                </div>
            </div>
        </div>
    </nav>
</div>

<style>
    .sb-sidenav {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        border-right: 1px solid rgba(255, 255, 255, 0.1);
    }

    .sb-sidenav-header {
        padding: 1rem 0.8rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        margin-bottom: 0.5rem;
    }

    .sb-sidenav-logo-icon {
        color: #3498db;
        background: rgba(52, 152, 219, 0.1);
        padding: 0.4rem;
        border-radius: 10px;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem !important;
    }

    .sb-sidenav-menu-heading {
        color: #95a5a6 !important;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 0.5rem;
    }

    .nav-link {
        color: #ecf0f1 !important;
        border-radius: 6px;
        transition: all 0.2s ease;
        position: relative;
        font-size: 0.85rem;
    }

    .nav-link:hover {
        background: rgba(52, 152, 219, 0.2) !important;
        transform: translateX(3px);
    }

    .nav-link.active {
        background: linear-gradient(135deg, #3498db 0%, #2980b9 100%) !important;
        box-shadow: 0 2px 8px rgba(52, 152, 219, 0.3);
    }

    .sb-nav-link-icon {
        color: #3498db;
        transition: all 0.2s ease;
        width: 20px;
        text-align: center;
    }

    .nav-link:hover .sb-nav-link-icon {
        color: #ecf0f1;
        transform: scale(1.05);
    }

    .sb-nav-link-badge {
        position: absolute;
        right: 0.5rem;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        color: #bdc3c7;
    }

    .sb-sidenav-collapse-arrow {
        color: #7f8c8d;
        transition: all 0.2s ease;
    }

    .nav-link:hover .sb-sidenav-collapse-arrow {
        color: #ecf0f1;
    }

    .collapse.show .sb-sidenav-collapse-arrow {
        transform: rotate(180deg);
    }

    .sb-sidenav-menu-nested .nav-link {
        border-left: 2px solid rgba(52, 152, 219, 0.3);
        font-size: 0.8rem;
    }

    .sb-sidenav-menu-nested .nav-link:hover {
        border-left-color: #3498db;
        background: rgba(52, 152, 219, 0.15) !important;
    }

    .sb-sidenav-footer {
        background: rgba(0, 0, 0, 0.2);
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .user-avatar {
        color: #3498db;
    }

    .nav {
        padding: 0.5rem 0;
    }

    .sb-sidenav-menu {
        max-height: calc(100vh - 120px);
        overflow-y: auto;
    }

    .sb-sidenav-menu::-webkit-scrollbar {
        width: 4px;
    }

    .sb-sidenav-menu::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 2px;
    }

    .sb-sidenav-menu::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.3);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const currentPath = window.location.pathname;
        const navLinks = document.querySelectorAll('.nav-link');

        navLinks.forEach(link => {
            if (link.href.includes(currentPath)) {
                link.classList.add('active');

                const parentCollapse = link.closest('.collapse');
                if (parentCollapse) {
                    const parentLink = document.querySelector(
                        `[data-bs-target="#${parentCollapse.id}"]`);
                    if (parentLink) {
                        parentLink.classList.remove('collapsed');
                        parentLink.setAttribute('aria-expanded', 'true');
                        parentCollapse.classList.add('show');
                    }
                }
            }
        });
    });
</script>
