@extends('layouts.app')

@section('title', 'Crear Corte de Tablero')

@push('css')
    <style>
        .form-container {
            background: #fff;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 1.5rem;
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .form-container:hover {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }

        .section-title {
            color: #2c3e50;
            font-weight: 700;
            font-size: 1.3rem;
            margin-bottom: 1.5rem;
            padding-bottom: 0.8rem;
            border-bottom: 3px solid transparent;
            border-image: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-image-slice: 1;
            display: flex;
            align-items: center;
            letter-spacing: -0.3px;
        }

        .section-title i {
            margin-right: 10px;
            font-size: 1.2em;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Solo afecta botones dentro del formulario principal */
        #corteForm button.btn,
        #corteForm .btn {
            border: none;
            padding: 0.7rem 1.4rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        #corteForm button.btn-primary,
        #corteForm .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        #corteForm button.btn-primary:hover,
        #corteForm .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }

        #corteForm button.btn-success,
        #corteForm .btn-success {
            background: linear-gradient(135deg, #27ae60, #229954);
            color: white;
            box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);
            border: none;
            border-radius: 10px;
        }

        #corteForm button.btn-success:hover,
        #corteForm .btn-success:hover {
            background: linear-gradient(135deg, #229954, #27ae60);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(39, 174, 96, 0.4);
        }

        #corteForm button.btn-outline-secondary,
        #corteForm .btn-outline-secondary {
            background: transparent;
            border: 2px solid #6c757d;
            color: #6c757d;
            box-shadow: none;
        }

        #corteForm button.btn-outline-secondary:hover,
        #corteForm .btn-outline-secondary:hover {
            background: #6c757d;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
        }

        #corteForm button.btn-lg,
        #corteForm .btn-lg {
            padding: 0.9rem 2.2rem;
            font-size: 1.15rem;
            border-radius: 10px;
        }

        #btn-imprimir {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            color: white !important;
            border: none !important;
            padding: 0.9rem 2.2rem !important;
            border-radius: 10px !important;
            font-weight: 600 !important;
            font-size: 1.15rem !important;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3) !important;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1) !important;
        }

        #btn-imprimir:hover {
            transform: translateY(-3px) !important;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4) !important;
        }

        .canvas-main-container {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 12px;
            padding: 2.5rem;
            margin-bottom: 2.5rem;
            border: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .canvas-container {
            border: 3px solid;
            border-image: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            border-image-slice: 1;
            border-radius: 10px;
            background: linear-gradient(135deg, #ffffff 0%, #fdfdfe 100%);
            min-height: 450px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            margin: 0 auto;
            max-width: 100%;
            box-shadow: inset 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        /* Quitar cualquier estilo que afecte al canvas directamente */
        #tableroCanvas {
            background: white;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08);
            border-radius: 5px;
        }

        .tablero-info {
            position: absolute;
            top: 15px;
            left: 15px;
            background: rgba(255, 255, 255, 0.95);
            padding: 12px 18px;
            border-radius: 8px;
            font-size: 0.9rem;
            border: 1px solid rgba(0, 0, 0, 0.08);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            backdrop-filter: blur(10px);
        }

        .grupos-list {
            max-height: 320px;
            overflow-y: auto;
            border: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: 10px;
            padding: 1.2rem;
            background: white;
            box-shadow: inset 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .grupo-item {
            background: linear-gradient(135deg, #f8f9fa 0%, #f1f3f5 100%);
            border: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 0.8rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .grupo-item:hover {
            background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
            transform: translateX(8px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .grupo-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .grupo-item:hover::before {
            opacity: 1;
        }

        .grupo-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.6rem;
        }

        .grupo-info {
            font-size: 0.95rem;
            flex: 1;
        }

        .grupo-medidas {
            font-family: 'Courier New', monospace;
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.85rem;
            margin-right: 0.8rem;
            font-weight: 600;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .grupo-controls {
            display: flex;
            gap: 0.4rem;
            align-items: center;
        }

        .grupo-cantidad {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            background: white;
            padding: 4px 8px;
            border-radius: 5px;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }

        .cantidad-input {
            width: 65px;
            text-align: center;
            padding: 0.3rem;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 600;
            background: #f8f9fa;
        }

        .btn-grupo {
            background: white;
            border: 1px solid rgba(0, 0, 0, 0.1);
            cursor: pointer;
            font-size: 0.9rem;
            padding: 0.3rem 0.5rem;
            border-radius: 5px;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        }

        .btn-grupo:hover {
            transform: translateY(-2px);
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
        }

        .btn-editar {
            color: #3498db;
            border-color: rgba(52, 152, 219, 0.3);
        }

        .btn-editar:hover {
            background: rgba(52, 152, 219, 0.1);
        }

        .btn-eliminar {
            color: #e74c3c;
            border-color: rgba(231, 76, 60, 0.3);
        }

        .btn-eliminar:hover {
            background: rgba(231, 76, 60, 0.1);
        }

        .grupo-descripcion {
            font-size: 0.85rem;
            color: #6c757d;
            margin-top: 0.4rem;
            padding-top: 0.4rem;
            border-top: 1px dashed rgba(0, 0, 0, 0.1);
        }

        .tableros-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 25px;
            margin-top: 25px;
        }

        .tablero-item {
            border: 2px solid rgba(0, 0, 0, 0.08);
            border-radius: 12px;
            padding: 18px;
            background: linear-gradient(135deg, #ffffff 0%, #fafbfc 100%);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
            overflow: hidden;
        }

        .tablero-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
            border-color: rgba(102, 126, 234, 0.3);
        }

        .tablero-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            opacity: 0.7;
        }

        .tablero-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
            padding-bottom: 10px;
            border-bottom: 2px solid rgba(0, 0, 0, 0.05);
        }

        .tablero-title {
            font-weight: 700;
            color: #2c3e50;
            font-size: 1.1rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .tablero-stats {
            font-size: 0.9rem;
            color: white;
            font-weight: 600;
            background: linear-gradient(135deg, #27ae60, #229954);
            padding: 5px 12px;
            border-radius: 20px;
            box-shadow: 0 2px 8px rgba(39, 174, 96, 0.3);
        }

        .tablero-canvas-container {
            position: relative;
            width: 100%;
            height: 160px;
            border: 2px solid rgba(0, 0, 0, 0.08);
            border-radius: 8px;
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: inset 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .tablero-canvas-container .tablero-mini-canvas {
            width: 100% !important;
            height: 100% !important;
            object-fit: contain !important;
            display: block !important;
        }

        .piezas-count {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(5px);
        }

        .tablero-vacio {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #95a5a6;
            font-style: italic;
            font-size: 0.9rem;
        }

        .tableros-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid rgba(0, 0, 0, 0.05);
        }

        .tablero-indicador {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 700;
            box-shadow: 0 3px 10px rgba(102, 126, 234, 0.3);
        }

        /* BARRA DE PROGRESO MEJORADA */
        .progress {
            height: 12px;
            border-radius: 10px;
            background: linear-gradient(135deg, #f1f3f5 0%, #e9ecef 100%);
            overflow: hidden;
            box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .progress-bar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: width 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.4);
            position: relative;
            overflow: hidden;
        }

        .progress-bar::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg,
                    transparent 0%,
                    rgba(255, 255, 255, 0.3) 50%,
                    transparent 100%);
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            0% {
                transform: translateX(-100%);
            }

            100% {
                transform: translateX(100%);
            }
        }

        .espacio-info {
            animation: slideIn 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
            background: linear-gradient(135deg, #3498db, #2980b9) !important;
            color: white;
            padding: 5px 10px !important;
            border-radius: 8px !important;
            font-weight: 600;
            box-shadow: 0 3px 10px rgba(52, 152, 219, 0.4);
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(10px) scale(0.95);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* ANIMACIONES GENERALES */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(15px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .tablero-item {
            animation: fadeInUp 0.5s ease forwards;
        }

        .grupo-item {
            animation: fadeInUp 0.4s ease forwards;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 0.75rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.15);
            background: white;
            transform: translateY(-1px);
        }

        @media (max-width: 992px) {
            .canvas-container {
                min-height: 400px;
            }

            .tableros-grid {
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                gap: 20px;
            }
        }

        @media (max-width: 768px) {
            .medidas-input {
                flex-direction: column;
                gap: 0.8rem;
            }

            .form-container {
                padding: 1.2rem;
            }

            .canvas-main-container {
                padding: 1.8rem;
            }

            .canvas-container {
                min-height: 350px;
            }

            .section-title {
                font-size: 1.2rem;
            }

            .tableros-grid {
                grid-template-columns: 1fr;
            }

            .grupo-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.8rem;
            }

            .grupo-controls {
                align-self: stretch;
                justify-content: flex-end;
            }

            #corteForm button.btn,
            #corteForm .btn {
                padding: 0.6rem 1rem;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 576px) {
            .canvas-main-container {
                padding: 1.2rem;
            }

            .tablero-item {
                padding: 15px;
            }

            .tablero-canvas-container {
                height: 140px;
            }
        }

        .btn-actualizar-tablero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            width: 100%;
            margin-top: 0.5rem;
            position: relative;
            overflow: hidden;
        }

        .btn-actualizar-tablero::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-actualizar-tablero:hover::before {
            left: 100%;
        }

        .btn-actualizar-tablero:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-actualizar-tablero:active {
            transform: translateY(0);
            box-shadow: 0 3px 10px rgba(102, 126, 234, 0.3);
        }

        .btn-actualizar-tablero i {
            margin-right: 0.5rem;
        }


        canvas {
            display: block !important;
            max-width: 100% !important;
            height: auto !important;
        }

        .tableros-grid canvas {
            width: 100% !important;
            height: 100% !important;
            object-fit: contain !important;
        }
    </style>
@endpush
@section('content')
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">Crear Corte de Tablero</h1>
            <a href="{{ route('cortes-tablero.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Volver
            </a>
        </div>

        <form action="{{ route('cortes-tablero.store') }}" method="POST" id="corteForm">
            @csrf

            <div class="row">
                <div class="col-lg-6">
                    <div class="form-container">
                        <div class="section-title">Medidas del Tablero</div>
                        <div class="form-group mb-2">
                            <label for="nombre_trabajo" class="form-label">Nombre del Trabajo:</label>
                            <input type="text" name="nombre_trabajo" id="nombre_trabajo"
                                class="form-control form-control-sm" placeholder="Ej: Mesa de centro" required>
                        </div>
                        <div class="form-group mb-2">
                            <label for="descripcion" class="form-label">Descripción:</label>
                            <textarea name="descripcion" id="descripcion" rows="2" class="form-control form-control-sm"
                                placeholder="Descripción del trabajo..."></textarea>
                        </div>
                        <div class="medidas-input">
                            <div class="form-group mb-2">
                                <label for="largo_tablero" class="form-label">Largo (cm):</label>
                                <input type="number" name="largo_tablero" id="largo_tablero"
                                    class="form-control form-control-sm" step="0.1" min="1" placeholder="0.0"
                                    required>
                            </div>
                            <div class="form-group mb-2">
                                <label for="ancho_tablero" class="form-label">Ancho (cm):</label>
                                <input type="number" name="ancho_tablero" id="ancho_tablero"
                                    class="form-control form-control-sm" step="0.1" min="1" placeholder="0.0"
                                    required>
                            </div>
                        </div>
                        <div class="form-group mb-2">
                            <label for="cantidad_tableros" class="form-label">Cantidad de Tableros:</label>
                            <input type="number" name="cantidad_tableros" id="cantidad_tableros"
                                class="form-control form-control-sm" min="1" value="1" required>
                        </div>
                        <button type="button" id="btn-actualizar-tablero" class="btn-actualizar-tablero">
                            <i class="fas fa-sync me-1"></i>Actualizar Vista
                        </button>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-container">
                        <div class="section-title">Grupos de Piezas</div>
                        <div class="medidas-input">
                            <div class="form-group mb-2">
                                <label for="largo_pieza" class="form-label">Largo Pieza (cm):</label>
                                <input type="number" id="largo_pieza" class="form-control form-control-sm" step="0.1"
                                    min="0.1" placeholder="0.0">
                            </div>
                            <div class="form-group mb-2">
                                <label for="ancho_pieza" class="form-label">Ancho Pieza (cm):</label>
                                <input type="number" id="ancho_pieza" class="form-control form-control-sm" step="0.1"
                                    min="0.1" placeholder="0.0">
                            </div>
                        </div>
                        <div class="form-group mb-2">
                            <label for="cantidad_pieza" class="form-label">Cantidad:</label>
                            <input type="number" id="cantidad_pieza" class="form-control form-control-sm" min="1"
                                value="1">
                        </div>
                        <div class="form-group mb-2">
                            <label for="descripcion_pieza" class="form-label">Descripción del Grupo:</label>
                            <input type="text" id="descripcion_pieza" class="form-control form-control-sm"
                                placeholder="Ej: Patas de mesa">
                        </div>
                        <button type="button" id="btn-agregar-grupo" class="btn btn-success btn-sm w-100 mb-3">
                            <i class="fas fa-plus me-1"></i>Agregar Grupo
                        </button>
                        <div class="grupos-list" id="grupos-list">
                            <div class="text-center text-muted py-3" id="empty-grupos">
                                <i class="fas fa-info-circle me-1"></i>No hay grupos agregados
                            </div>
                        </div>
                        <div class="mt-3 p-2 bg-light rounded">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Total Grupos:</span>
                                <span id="total-grupos" class="fw-bold">0</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Total Piezas:</span>
                                <span id="total-piezas" class="fw-bold">0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección Central: Visualización del Tablero -->
            <div class="canvas-main-container">
                <div class="section-title text-center">
                    <i class="fas fa-eye me-2"></i>Visualización del Corte
                </div>

                <!-- Vista principal del canvas -->
                <div class="canvas-container">
                    <canvas id="tableroCanvas" width="400" height="400"></canvas>
                    <div class="tablero-info">
                        <div id="tableroSize">0 x 0 cm</div>
                        <div id="tableroArea">Área: 0 cm²</div>
                    </div>
                </div>

                <!-- Grid de múltiples tableros -->
                <div class="tableros-controls">
                    <h5 class="mb-0">Distribución en Tableros</h5>
                    <span class="tablero-indicador" id="tableros-count">1 Tablero</span>
                </div>

                <div class="tableros-grid" id="tableros-grid">
                    <!-- Los tableros se generarán dinámicamente aquí -->
                </div>

                <div class="row mt-4">
                    <div class="col-md-6 offset-md-3 text-center">
                        <div id="eficienciaText" class="text-muted mb-2">Eficiencia: 0%</div>
                        <div class="progress" style="height: 12px;">
                            <div id="eficienciaProgress" class="progress-bar" role="progressbar" style="width: 0%">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botón de Imprimir -->
            <div class="row mt-4">
                <div class="col-12 text-center">
                    <button type="button" id="btn-imprimir" class="btn btn-primary btn-lg">
                        <i class="fas fa-print me-2"></i>Imprimir
                    </button>
                </div>
            </div>

            <!-- Hidden input para almacenar los grupos -->
            <input type="hidden" name="grupos" id="grupos-input">
        </form>
        <iframe id="hiddenVentaPdfIframe" style="display: none; position: absolute; left: -9999px; width: 0; height: 0;"
            width="0" height="0"></iframe>
    </div>


@endsection

@push('js')
    <script>
        class MaxRectsPacker {
            constructor(width, height) {
                this.width = width;
                this.height = height;
                this.freeRects = [{
                    x: 0,
                    y: 0,
                    width: width,
                    height: height
                }];
                this.usedRects = [];
            }

            insert(pieceWidth, pieceHeight, allowRotation = true) {
                let bestNode = null;
                let bestScore = Infinity;

                // Probar ambas orientaciones
                const orientations = allowRotation ? [
                    [pieceWidth, pieceHeight, false],
                    [pieceHeight, pieceWidth, true]
                ] : [
                    [pieceWidth, pieceHeight, false]
                ];

                for (const [w, h, rotated] of orientations) {
                    for (const freeRect of this.freeRects) {
                        if (freeRect.width < w || freeRect.height < h) continue;

                        // Heurística: preferir rectángulos que dejen menos espacio sobrante
                        const score = freeRect.width * freeRect.height - w * h;

                        if (score < bestScore) {
                            bestScore = score;
                            bestNode = {
                                x: freeRect.x,
                                y: freeRect.y,
                                width: w,
                                height: h,
                                rotated: rotated
                            };
                        }
                    }
                }

                if (!bestNode) return null;

                // Dividir el rectángulo libre usado
                this._splitFreeRects(bestNode);

                // Limpiar rectángulos libres que sean muy pequeños
                this._cleanFreeRects();

                this.usedRects.push(bestNode);
                return bestNode;
            }

            _splitFreeRects(usedNode) {
                const newFreeRects = [];

                for (const freeRect of this.freeRects) {
                    // Si no se intersecan, mantener el rectángulo libre
                    if (usedNode.x >= freeRect.x + freeRect.width ||
                        usedNode.x + usedNode.width <= freeRect.x ||
                        usedNode.y >= freeRect.y + freeRect.height ||
                        usedNode.y + usedNode.height <= freeRect.y) {
                        newFreeRects.push(freeRect);
                        continue;
                    }

                    // Crear rectángulos libres del espacio sobrante
                    // Espacio a la izquierda
                    if (usedNode.x > freeRect.x) {
                        newFreeRects.push({
                            x: freeRect.x,
                            y: freeRect.y,
                            width: usedNode.x - freeRect.x,
                            height: freeRect.height
                        });
                    }

                    // Espacio a la derecha
                    if (usedNode.x + usedNode.width < freeRect.x + freeRect.width) {
                        newFreeRects.push({
                            x: usedNode.x + usedNode.width,
                            y: freeRect.y,
                            width: freeRect.x + freeRect.width - (usedNode.x + usedNode.width),
                            height: freeRect.height
                        });
                    }

                    // Espacio arriba
                    if (usedNode.y > freeRect.y) {
                        newFreeRects.push({
                            x: freeRect.x,
                            y: freeRect.y,
                            width: freeRect.width,
                            height: usedNode.y - freeRect.y
                        });
                    }

                    // Espacio abajo
                    if (usedNode.y + usedNode.height < freeRect.y + freeRect.height) {
                        newFreeRects.push({
                            x: freeRect.x,
                            y: usedNode.y + usedNode.height,
                            width: freeRect.width,
                            height: freeRect.y + freeRect.height - (usedNode.y + usedNode.height)
                        });
                    }
                }

                this.freeRects = newFreeRects;
            }

            _cleanFreeRects() {
                // Eliminar rectángulos libres demasiado pequeños (menos de 1 cm)
                this.freeRects = this.freeRects.filter(rect =>
                    rect.width >= 1 && rect.height >= 1
                );
            }

            get efficiency() {
                const usedArea = this.usedRects.reduce((sum, rect) =>
                    sum + (rect.width * rect.height), 0);
                const totalArea = this.width * this.height;
                return totalArea > 0 ? (usedArea / totalArea) * 100 : 0;
            }
        }

        /* ----------  ALGORITMO DE OPTIMIZACIÓN DE CORTES  ---------- */
        function layoutOptimo() {
            const numTableros = parseInt(cantT.value) || 1;
            generarPiezasDesdeGrupos();

            // Crear una representación de grid para cada tablero
            const grids = Array.from({
                    length: numTableros
                }, () =>
                crearGridVacio()
            );

            const tableros = Array.from({
                length: numTableros
            }, () => []);

            // Agrupar piezas por tamaño y ordenar por área
            const grupos = agruparPiezasPorTamaño(piezas);

            // Colocar piezas de cada grupo
            for (const grupo of grupos) {
                for (let i = 0; i < grupo.cantidad; i++) {
                    const colocada = colocarPiezaEnGrid(
                        grupo.l,
                        grupo.a,
                        grids,
                        tableros,
                        // CAMBIO AQUÍ: Mostrar medidas en lugar de grupo
                        `${grupo.l}×${grupo.a} cm` // Ejemplo: "50×100 cm"
                    );

                    if (!colocada) {
                        // Marcar como no colocada
                        tableros[0].push({
                            id: `${grupo.id}-${i}`,
                            l: grupo.l,
                            a: grupo.a,
                            grupoId: grupo.id,
                            descripcion: `${grupo.l}×${grupo.a} cm`, // También aquí
                            x: -1,
                            y: -1,
                            rot: false,
                            tablero: -1
                        });
                    }
                }
            }

            return tableros;
        }

        function crearGridVacio() {
            const grid = [];
            for (let y = 0; y < tablero.h; y++) {
                grid[y] = [];
                for (let x = 0; x < tablero.w; x++) {
                    grid[y][x] = false; // false = espacio libre
                }
            }
            return grid;
        }

        function agruparPiezasPorTamaño(piezas) {
            const gruposMap = {};

            piezas.forEach(p => {
                const key = `${p.l}x${p.a}`;
                if (!gruposMap[key]) {
                    gruposMap[key] = {
                        l: p.l,
                        a: p.a,
                        cantidad: 0,
                        id: p.grupoId,
                        descripcion: p.descripcion
                    };
                }
                gruposMap[key].cantidad++;
            });

            // Convertir a array y ordenar por área descendente
            return Object.values(gruposMap).sort((a, b) =>
                (b.l * b.a) - (a.l * a.a)
            );
        }

        function colocarPiezaEnGrid(l, a, grids, tableros, descripcion) {
            // Probar ambos orientaciones
            const orientaciones = [{
                    w: l,
                    h: a,
                    rot: false
                },
                {
                    w: a,
                    h: l,
                    rot: true
                }
            ];

            // Probar en cada tablero
            for (let t = 0; t < grids.length; t++) {
                for (const orientacion of orientaciones) {
                    const posicion = encontrarEspacioEnGrid(grids[t], orientacion.w, orientacion.h);

                    if (posicion) {
                        // Marcar el espacio como ocupado
                        marcarEspacioOcupado(grids[t], posicion.x, posicion.y, orientacion.w, orientacion.h);

                        // Añadir a las piezas del tablero
                        tableros[t].push({
                            id: `piece-${Date.now()}-${Math.random()}`,
                            l: l,
                            a: a,
                            grupoId: descripcion,
                            descripcion: descripcion,
                            x: posicion.x,
                            y: posicion.y,
                            rot: orientacion.rot,
                            tablero: t
                        });

                        return true;
                    }
                }
            }

            return false;
        }

        function encontrarEspacioEnGrid(grid, w, h) {
            // Buscar de abajo hacia arriba, izquierda a derecha (estrategia bottom-left)
            for (let y = 0; y <= tablero.h - h; y++) {
                for (let x = 0; x <= tablero.w - w; x++) {
                    let espacioLibre = true;

                    // Verificar todas las celdas necesarias
                    for (let dy = 0; dy < h && espacioLibre; dy++) {
                        for (let dx = 0; dx < w && espacioLibre; dx++) {
                            if (grid[y + dy][x + dx]) {
                                espacioLibre = false;
                            }
                        }
                    }

                    if (espacioLibre) {
                        return {
                            x,
                            y
                        };
                    }
                }
            }

            return null;
        }

        function marcarEspacioOcupado(grid, x, y, w, h) {
            for (let dy = 0; dy < h; dy++) {
                for (let dx = 0; dx < w; dx++) {
                    if (y + dy < grid.length && x + dx < grid[0].length) {
                        grid[y + dy][x + dx] = true;
                    }
                }
            }
        }

        // Reemplaza tu función layout actual con esta:
        function layout() {
            // Usar el algoritmo optimizado
            return layoutOptimo();
        }


        /* ----------  CONFIG  ---------- */
        const canvas = document.getElementById('tableroCanvas');
        const ctx = canvas.getContext('2d');
        const PIEZA_COLOR = '#3498db';

        /* ----------  ESTADO  ---------- */
        let tablero = {
            w: 0,
            h: 0
        }; // cm
        let grupos = []; // {id, l, a, cantidad, descripcion}
        let nextGrupoId = 1;
        let piezas = []; // Se generan a partir de los grupos
        let tablerosLayout = []; // Array de tableros con sus piezas
        let grupoEditando = null;

        /* ----------  ACCESOS  ---------- */
        const $ = id => document.getElementById(id);
        const largoT = $('largo_tablero');
        const anchoT = $('ancho_tablero');
        const cantT = $('cantidad_tableros');
        const btnAdd = $('btn-agregar-grupo');
        const btnUpd = $('btn-actualizar-tablero');
        const listDiv = $('grupos-list');
        const inputGrupos = $('grupos-input');
        const btnImprimir = $('btn-imprimir');
        const tablerosGrid = $('tableros-grid');
        const tablerosCount = $('tableros-count');

        /* ----------  MATEMÁGICAS  ---------- */
        const area = (w, h) => w * h;

        function generarPiezasDesdeGrupos() {
            piezas = [];
            grupos.forEach(grupo => {
                for (let i = 0; i < grupo.cantidad; i++) {
                    piezas.push({
                        id: `${grupo.id}-${i}`,
                        l: grupo.l,
                        a: grupo.a,
                        grupoId: grupo.id,
                        descripcion: grupo.descripcion,
                        rot: false,
                        x: -1,
                        y: -1,
                        tablero: -1
                    });
                }
            });
        }

        function overlap(x, y, w, h, list) {
            return list.some(o => {
                let ow = o.rot ? o.a : o.l;
                let oh = o.rot ? o.l : o.a;
                return !(x >= o.x + ow || x + w <= o.x || y >= o.y + oh || y + h <= o.y);
            });
        }

        function calcularEspaciosRestantes(tableroIndex) {
            const piezasEnTablero = tablerosLayout[tableroIndex] || [];

            // Crear grid para este tablero
            const grid = Array(tablero.h).fill().map(() => Array(tablero.w).fill(false));

            // Marcar áreas ocupadas por piezas
            piezasEnTablero.forEach(p => {
                if (p.x >= 0 && p.y >= 0) {
                    const w = p.rot ? p.a : p.l;
                    const h = p.rot ? p.l : p.a;

                    for (let y = p.y; y < p.y + h && y < tablero.h; y++) {
                        for (let x = p.x; x < p.x + w && x < tablero.w; x++) {
                            if (grid[y] && grid[y][x] !== undefined) {
                                grid[y][x] = true;
                            }
                        }
                    }
                }
            });

            // Encontrar espacios libres (rectángulos máximos)
            const espacios = [];
            const visitado = Array(tablero.h).fill().map(() => Array(tablero.w).fill(false));

            for (let y = 0; y < tablero.h; y++) {
                for (let x = 0; x < tablero.w; x++) {
                    if (!grid[y][x] && !visitado[y][x]) {
                        // Encontrar rectángulo máximo desde esta posición
                        const espacio = encontrarRectanguloMaximo(grid, x, y, visitado);
                        if (espacio && espacio.area > 0) {
                            espacios.push(espacio);
                        }
                    }
                }
            }

            return espacios.sort((a, b) => b.area - a.area); // Ordenar por área descendente
        }

        function encontrarRectanguloMaximo(grid, startX, startY, visitado) {
            let maxAncho = 0;
            let maxAlto = 0;

            // Encontrar ancho máximo en esta fila
            for (let x = startX; x < tablero.w && !grid[startY][x]; x++) {
                maxAncho++;
            }

            // Encontrar alto máximo en esta columna
            for (let y = startY; y < tablero.h; y++) {
                let filaLibre = true;
                for (let x = startX; x < startX + maxAncho && x < tablero.w; x++) {
                    if (grid[y][x]) {
                        filaLibre = false;
                        break;
                    }
                }
                if (filaLibre) {
                    maxAlto++;
                } else {
                    break;
                }
            }

            // Marcar como visitado
            for (let y = startY; y < startY + maxAlto && y < tablero.h; y++) {
                for (let x = startX; x < startX + maxAncho && x < tablero.w; x++) {
                    if (visitado[y]) {
                        visitado[y][x] = true;
                    }
                }
            }

            return {
                x: startX,
                y: startY,
                ancho: maxAncho,
                alto: maxAlto,
                area: maxAncho * maxAlto
            };
        }

        /* ----------  DIBUJO MEJORADO  ---------- */
        function draw() {
            if (!canvas) return;

            ctx.clearRect(0, 0, canvas.width, canvas.height);

            const tableroIndex = 0;
            const piezasEnTablero = tablerosLayout[tableroIndex] || [];

            // Calcular espacios restantes
            const espaciosRestantes = calcularEspaciosRestantes(tableroIndex);

            let margin = 20;
            let scaleX = (canvas.width - 2 * margin) / tablero.w;
            let scaleY = (canvas.height - 2 * margin) / tablero.h;
            let scale = Math.min(scaleX, scaleY);
            let offX = (canvas.width - scale * tablero.w) / 2;
            let offY = (canvas.height - scale * tablero.h) / 2;

            // Tablero con fondo
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(offX, offY, scale * tablero.w, scale * tablero.h);
            ctx.strokeStyle = '#2c3e50';
            ctx.lineWidth = 3;
            ctx.strokeRect(offX, offY, scale * tablero.w, scale * tablero.h);

            // 1. Dibujar espacios restantes primero (como fondo)
            espaciosRestantes.forEach((espacio, index) => {
                if (espacio.area > 0) {
                    const px = offX + scale * espacio.x;
                    const py = offY + scale * (tablero.h - espacio.alto - espacio.y);

                    // Color para espacios (muy suave)
                    ctx.fillStyle = index === 0 ? 'rgba(230, 245, 255, 0.5)' : 'rgba(245, 245, 245, 0.3)';
                    ctx.fillRect(px, py, scale * espacio.ancho, scale * espacio.alto);

                    // Borde punteado para espacios
                    ctx.strokeStyle = index === 0 ? 'rgba(52, 152, 219, 0.3)' : 'rgba(200, 200, 200, 0.2)';
                    ctx.lineWidth = 1;
                    ctx.setLineDash([3, 3]);
                    ctx.strokeRect(px, py, scale * espacio.ancho, scale * espacio.alto);
                    ctx.setLineDash([]);

                    // Etiqueta para el espacio más grande
                    if (index === 0 && scale * espacio.ancho > 30 && scale * espacio.alto > 20) {
                        ctx.fillStyle = 'rgba(52, 152, 219, 0.8)';
                        ctx.font = 'bold 10px Arial, sans-serif';
                        const txt = `Espacio: ${espacio.ancho}×${espacio.alto}`;
                        const textWidth = ctx.measureText(txt).width;
                        const centerX = px + (scale * espacio.ancho) / 2;
                        const centerY = py + (scale * espacio.alto) / 2;

                        ctx.fillText(txt, centerX - textWidth / 2, centerY);
                    }
                }
            });

            // 2. Dibujar piezas encima
            let used = 0;
            piezasEnTablero.forEach((p) => {
                if (p.x < 0) return;

                let w = p.rot ? p.a : p.l;
                let h = p.rot ? p.l : p.a;
                let px = offX + scale * p.x;
                let py = offY + scale * (tablero.h - h - p.y);

                // Color sólido para piezas
                ctx.fillStyle = PIEZA_COLOR;
                ctx.fillRect(px, py, scale * w, scale * h);

                // Borde más visible
                ctx.strokeStyle = '#1a5276';
                ctx.lineWidth = 1.5;
                ctx.strokeRect(px, py, scale * w, scale * h);

                // Etiqueta mejorada - Mostrar medidas
                if (scale * w > 25 && scale * h > 15) {
                    ctx.fillStyle = '#ffffff';
                    ctx.font = 'bold 11px Arial, sans-serif';

                    let txt = `${p.l}×${p.a}`;
                    const textWidth = ctx.measureText(txt).width;
                    const centerX = px + (scale * w) / 2;
                    const centerY = py + (scale * h) / 2;

                    // Fondo para texto
                    ctx.fillStyle = 'rgba(0, 0, 0, 0.5)';
                    ctx.fillRect(
                        centerX - textWidth / 2 - 3,
                        centerY - 8,
                        textWidth + 6,
                        16
                    );

                    // Texto
                    ctx.fillStyle = '#ffffff';
                    ctx.fillText(txt, centerX - textWidth / 2, centerY + 4);
                }

                used += area(w, h);
            });

            // 3. Mostrar información de espacios en la esquina
            mostrarInfoEspacios(espaciosRestantes, offX, offY);

            updateEfficiency(used);
            updateTablerosGrid();
        }

        function mostrarInfoEspacios(espacios, offX, offY) {
            // Mostrar información en la esquina superior derecha
            const espaciosGrandes = espacios.filter(e => e.area >= 100); // Espacios de al menos 100cm²
            const areaTotalEspacios = espacios.reduce((sum, e) => sum + e.area, 0);

            if (espaciosGrandes.length > 0) {
                ctx.fillStyle = 'rgba(255, 255, 255, 0.9)';
                ctx.fillRect(offX + 10, offY + 10, 180, 20 + (espaciosGrandes.length * 18));

                ctx.fillStyle = '#2c3e50';
                ctx.font = 'bold 11px Arial, sans-serif';
                ctx.fillText('Espacios disponibles:', offX + 15, offY + 25);

                ctx.font = '10px Arial, sans-serif';
                espaciosGrandes.slice(0, 3).forEach((espacio, i) => {
                    const yPos = offY + 45 + (i * 16);
                    ctx.fillText(`${i+1}. ${espacio.ancho}×${espacio.alto} cm (${espacio.area} cm²)`,
                        offX + 20, yPos);
                });

                if (espaciosGrandes.length > 3) {
                    ctx.fillText(`... y ${espaciosGrandes.length - 3} más`, offX + 20, offY + 45 + (3 * 16));
                }
            }
        }

        function drawMiniTablero(canvas, piezasEnTablero, tableroIndex) {
            const ctx = canvas.getContext('2d');
            const width = canvas.width;
            const height = canvas.height;

            ctx.clearRect(0, 0, width, height);

            // Fondo blanco
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, width, height);

            // Escala
            let margin = 8;
            let scaleX = (width - 2 * margin) / tablero.w;
            let scaleY = (height - 2 * margin) / tablero.h;
            let scale = Math.min(scaleX, scaleY);
            let offX = (width - scale * tablero.w) / 2;
            let offY = (height - scale * tablero.h) / 2;

            // Marco del tablero
            ctx.strokeStyle = '#4a6572';
            ctx.lineWidth = 1.5;
            ctx.strokeRect(offX, offY, scale * tablero.w, scale * tablero.h);

            // Calcular y dibujar espacios restantes (solo el más grande)
            const espacios = calcularEspaciosRestantes(tableroIndex);
            if (espacios.length > 0 && espacios[0].area > 0) {
                const espacio = espacios[0]; // Solo el más grande
                const px = offX + scale * espacio.x;
                const py = offY + scale * (tablero.h - espacio.alto - espacio.y);

                ctx.fillStyle = 'rgba(230, 245, 255, 0.4)';
                ctx.fillRect(px, py, scale * espacio.ancho, scale * espacio.alto);

                ctx.strokeStyle = 'rgba(52, 152, 219, 0.4)';
                ctx.lineWidth = 0.8;
                ctx.setLineDash([2, 2]);
                ctx.strokeRect(px, py, scale * espacio.ancho, scale * espacio.alto);
                ctx.setLineDash([]);
            }

            // Dibujar piezas
            piezasEnTablero.forEach((p) => {
                if (p.x < 0) return;

                let w = p.rot ? p.a : p.l;
                let h = p.rot ? p.l : p.a;
                let px = offX + scale * p.x;
                let py = offY + scale * (tablero.h - h - p.y);

                // Color sólido
                ctx.fillStyle = PIEZA_COLOR;
                ctx.fillRect(px, py, scale * w, scale * h);

                // Borde
                ctx.strokeStyle = '#1a5276';
                ctx.lineWidth = 0.6;
                ctx.strokeRect(px, py, scale * w, scale * h);

                // Etiqueta para piezas grandes
                if (scale * w > 20 && scale * h > 12) {
                    ctx.fillStyle = '#ffffff';
                    ctx.font = 'bold 7px Arial, sans-serif';

                    let txt = `${p.l}×${p.a}`;
                    const textWidth = ctx.measureText(txt).width;
                    const centerX = px + (scale * w) / 2;
                    const centerY = py + (scale * h) / 2;

                    if (textWidth < scale * w - 4) {
                        ctx.fillStyle = 'rgba(0, 0, 0, 0.6)';
                        ctx.fillRect(
                            centerX - textWidth / 2 - 1,
                            centerY - 4,
                            textWidth + 2,
                            8
                        );

                        ctx.fillStyle = '#ffffff';
                        ctx.fillText(txt, centerX - textWidth / 2, centerY + 2);
                    }
                }
            });
        }

        function updateTablerosGrid() {
            if (!tablerosGrid) return;

            const numTableros = parseInt(cantT.value) || 1;
            tablerosGrid.innerHTML = '';

            if (tablerosCount) {
                tablerosCount.textContent = `${numTableros} Tablero${numTableros > 1 ? 's' : ''}`;
            }

            for (let i = 0; i < numTableros; i++) {
                const piezasEnTablero = tablerosLayout[i] || [];
                const piezasColocadas = piezasEnTablero.filter(p => p.x >= 0).length;
                const areaUsada = piezasEnTablero.reduce((sum, p) => {
                    if (p.x < 0) return sum;
                    let w = p.rot ? p.a : p.l;
                    let h = p.rot ? p.l : p.a;
                    return sum + area(w, h);
                }, 0);

                // Calcular espacios restantes
                const espacios = calcularEspaciosRestantes(i);
                const areaTotalEspacios = espacios.reduce((sum, e) => sum + e.area, 0);
                const espacioMasGrande = espacios.length > 0 ? espacios[0] : null;

                const eficiencia = tablero.w * tablero.h > 0 ?
                    ((areaUsada / (tablero.w * tablero.h)) * 100).toFixed(1) : 0;

                // Crear texto con información de espacios
                let infoEspacios = '';
                if (espacioMasGrande && espacioMasGrande.area > 0) {
                    infoEspacios = ` | Espacio: ${espacioMasGrande.ancho}×${espacioMasGrande.alto}`;
                }

                const tableroDiv = document.createElement('div');
                tableroDiv.className = 'tablero-item';
                tableroDiv.innerHTML = `
            <div class="tablero-header">
                <div>
                    <div class="tablero-title">Tablero ${i + 1}</div>
                    <div class="tablero-subtitle" style="font-size: 0.75rem; color: #666; margin-top: 2px;">
                        ${eficiencia}% uso${infoEspacios}
                    </div>
                </div>
                <div class="tablero-stats">${piezasColocadas} piezas</div>
            </div>
            <div class="tablero-canvas-container">
                ${piezasColocadas > 0 ?
                    `<canvas class="tablero-mini-canvas" width="280" height="150" data-tablero="${i}"></canvas>
                                                                 ${espacioMasGrande && espacioMasGrande.area > 100 ?
                                                                    `<div class="espacio-info" style="position: absolute; bottom: 30px; left: 8px; background: rgba(52, 152, 219, 0.9); color: white; padding: 2px 6px; border-radius: 3px; font-size: 0.65rem;">
                            Mayor espacio: ${espacioMasGrande.ancho}×${espacioMasGrande.alto}
                        </div>` : ''}
                                                                 <div class="piezas-count">${piezasColocadas} piezas</div>` :
                    `<div class="tablero-vacio">Sin piezas</div>`
                }
            </div>
        `;
                tablerosGrid.appendChild(tableroDiv);

                if (piezasColocadas > 0) {
                    const miniCanvas = tableroDiv.querySelector('.tablero-mini-canvas');
                    setTimeout(() => {
                        drawMiniTablero(miniCanvas, piezasEnTablero, i);
                    }, 100);
                }
            }
        }

        function updateEfficiency(used) {
            let total = area(tablero.w, tablero.h) * parseInt(cantT.value || 1);
            let waste = total - used;
            let eff = total ? ((used / total) * 100).toFixed(1) : 0;

            const eficienciaText = $('eficienciaText');
            const eficienciaProgress = $('eficienciaProgress');
            const tableroSize = $('tableroSize');
            const tableroArea = $('tableroArea');

            if (eficienciaText) {
                eficienciaText.textContent = `Eficiencia: ${eff}% (sobra ${waste.toFixed(1)} cm²)`;
            }

            if (eficienciaProgress) {
                eficienciaProgress.style.width = eff + '%';
            }

            if (tableroSize) {
                tableroSize.textContent = `${tablero.w} x ${tablero.h} cm`;
            }

            if (tableroArea) {
                tableroArea.textContent = `Área: ${(tablero.w * tablero.h).toFixed(1)} cm²`;
            }
        }

        /* ----------  CRUD GRUPOS  ---------- */
        function renderList() {
            if (!listDiv) return;

            listDiv.innerHTML = '';

            if (!grupos.length) {
                listDiv.innerHTML = '<div class="text-muted"><i class="fas fa-info-circle me-1"></i>No hay grupos</div>';
            } else {
                grupos.forEach((grupo) => {
                    let div = document.createElement('div');
                    div.className = 'grupo-item';
                    div.innerHTML = `
						<div class="grupo-header">
							<div class="grupo-info">
								<span class="grupo-medidas">${grupo.l} × ${grupo.a} cm</span>
								<strong>${grupo.descripcion || 'Sin descripción'}</strong>
							</div>
							<div class="grupo-controls">
								<div class="grupo-cantidad">
									<span>Cant:</span>
									<input type="number" class="cantidad-input" value="${grupo.cantidad}"
										   min="1" data-id="${grupo.id}">
								</div>
								<button class="btn-grupo btn-eliminar" data-id="${grupo.id}" title="Eliminar">
									<i class="fas fa-trash"></i>
								</button>
							</div>
						</div>
						<div class="grupo-descripcion">
							Grupo ID: ${grupo.id} | Total piezas: ${grupo.cantidad}
						</div>
					`;
                    listDiv.appendChild(div);
                });

                listDiv.querySelectorAll('.cantidad-input').forEach(input => {
                    input.addEventListener('change', e => {
                        let id = parseInt(e.target.dataset.id);
                        let nuevaCantidad = parseInt(e.target.value);
                        if (nuevaCantidad > 0) {
                            actualizarCantidadGrupo(id, nuevaCantidad);
                        } else {
                            e.target.value = 1;
                        }
                    });
                });

                listDiv.querySelectorAll('.btn-eliminar').forEach(btn => {
                    btn.addEventListener('click', e => {
                        let id = parseInt(e.target.closest('.btn-eliminar').dataset.id);
                        eliminarGrupo(id);
                    });
                });
            }

            const totalGrupos = $('total-grupos');
            const totalPiezas = $('total-piezas');

            if (totalGrupos) {
                totalGrupos.textContent = grupos.length;
            }

            if (totalPiezas) {
                totalPiezas.textContent = grupos.reduce((sum, g) => sum + g.cantidad, 0);
            }

            if (inputGrupos) {
                inputGrupos.value = JSON.stringify(grupos);
            }
        }

        function agregarGrupo() {
            const largoPieza = $('largo_pieza');
            const anchoPieza = $('ancho_pieza');
            const cantidadPieza = $('cantidad_pieza');
            const descripcionPieza = $('descripcion_pieza');

            if (!largoPieza || !anchoPieza || !cantidadPieza) {
                alert('No se pueden encontrar los campos de entrada');
                return;
            }

            let l = parseFloat(largoPieza.value) || 0;
            let a = parseFloat(anchoPieza.value) || 0;
            let c = parseInt(cantidadPieza.value) || 1;
            let d = descripcionPieza ? descripcionPieza.value.trim() : '';

            if (l <= 0 || a <= 0) {
                alert('Medidas inválidas');
                return;
            }

            const grupoExistente = grupos.find(g =>
                g.l === l && g.a === a && g.descripcion === d
            );

            if (grupoExistente) {
                grupoExistente.cantidad += c;
            } else {
                grupos.push({
                    id: nextGrupoId++,
                    l,
                    a,
                    cantidad: c,
                    descripcion: d
                });
            }

            refresh();

            largoPieza.value = '';
            anchoPieza.value = '';
            cantidadPieza.value = 1;
            if (descripcionPieza) descripcionPieza.value = '';
        }

        function actualizarCantidadGrupo(id, nuevaCantidad) {
            const grupo = grupos.find(g => g.id === id);
            if (grupo) {
                grupo.cantidad = nuevaCantidad;
                refresh();
            }
        }

        function eliminarGrupo(id) {
            if (confirm('¿Estás seguro de que quieres eliminar este grupo?')) {
                grupos = grupos.filter(g => g.id !== id);
                refresh();
            }
        }

        function refresh() {
            tablerosLayout = layout();
            draw();
            renderList();
        }

        /* ----------  IMPRIMIR  ---------- */
        function imprimirCorte() {
            const hiddenIframe = document.getElementById("hiddenVentaPdfIframe");

            if (grupos.length === 0) {
                alert('No hay grupos para imprimir. Agregue al menos un grupo.');
                return;
            }

            if (tablero.w <= 0 || tablero.h <= 0) {
                alert('Las medidas del tablero no son válidas.');
                return;
            }

            const totalArea = tablero.w * tablero.h * (parseInt(cantT.value) || 1);
            const usedArea = piezas.reduce((sum, p) => {
                if (p.x < 0) return sum;
                let w = p.rot ? p.a : p.l;
                let h = p.rot ? p.l : p.a;
                return sum + (w * h);
            }, 0);
            const eficienciaTotal = totalArea > 0 ? ((usedArea / totalArea) * 100).toFixed(1) : 0;

            const canvases = document.querySelectorAll("#tableros-grid canvas");

            let contenidoImpresion = `
		<!DOCTYPE html>
		<html>
		<head>
			<title>Corte - ${$('nombre_trabajo').value || 'Sin nombre'}</title>
			<style>
				* { margin: 0; padding: 0; box-sizing: border-box; }
				body { font-family: Arial, sans-serif; font-size: 12px; margin: 0.5cm; line-height: 1.2; }
				.header { text-align: center; margin-bottom: 8px; padding-bottom: 5px; border-bottom: 2px solid #333; }
				.header h1 { font-size: 16px; margin-bottom: 3px; }
				.info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin: 8px 0; font-size: 11px; }
				.info-item { padding: 4px; background: #f5f5f5; border-radius: 3px; }
				.grupos-table { width: 100%; border-collapse: collapse; margin: 8px 0; font-size: 10px; }
				.grupos-table th { background: #333; color: white; padding: 4px 3px; font-weight: bold; }
				.grupos-table td { padding: 3px 2px; border: 1px solid #ddd; text-align: center; }
				.tableros-section { margin: 8px 0; }
				.tablero-item { border: 1px solid #ccc; padding: 4px; margin-bottom: 10px; text-align: center; background: #f9f9f9; border-radius: 3px; }
				.tablero-title { font-weight: bold; font-size: 10px; margin-bottom: 2px; }
				.tablero-stats { font-size: 9px; color: #666; margin-bottom: 5px; }
				.no-print { display: none; }
				.total-row { background: #e8f4fd; font-weight: bold; }
				.grupo-row { background: #f0f0f0; }
				@media print {
					body { margin: 0.3cm; font-size: 10px; }
					.header h1 { font-size: 14px; }
					.grupos-table { font-size: 9px; }
					.info-grid { font-size: 9px; }
				}
				.fecha { text-align: right; font-size: 9px; color: #666; margin-bottom: 5px; }
			</style>
		</head>
		<body>
			<div class="fecha">${new Date().toLocaleDateString()}</div>

			<div class="header">
				<h1>CORTE DE TABLERO</h1>
				<div>${$('nombre_trabajo').value || 'Sin nombre específico'}</div>
			</div>

			<div class="info-grid">
				<div class="info-item"><strong>Tablero:</strong> ${tablero.w} × ${tablero.h} cm</div>
				<div class="info-item"><strong>Cantidad:</strong> ${cantT.value || 1} tablero(s)</div>
				<div class="info-item"><strong>Total Grupos:</strong> ${grupos.length}</div>
				<div class="info-item"><strong>Total Piezas:</strong> ${grupos.reduce((sum, g) => sum + g.cantidad, 0)}</div>
				<div class="info-item"><strong>Eficiencia:</strong> ${eficienciaTotal}%</div>
			</div>

			<h3 style="margin: 8px 0 4px 0; font-size: 12px;">GRUPOS DE PIEZAS</h3>
			<table class="grupos-table">
				<thead>
					<tr>
						<th width="5%">#</th>
						<th width="25%">Descripción</th>
						<th width="12%">Largo</th>
						<th width="12%">Ancho</th>
						<th width="8%">Cant</th>
						<th width="38%">Notas</th>
					</tr>
				</thead>
				<tbody>
					${grupos.map((g, idx) => `
                                                                                                                								<tr class="grupo-row">
                                                                                                                									<td>${idx + 1}</td>
                                                                                                                									<td>${g.descripcion || '-'}</td>
                                                                                                                									<td>${g.l} cm</td>
                                                                                                                									<td>${g.a} cm</td>
                                                                                                                									<td>${g.cantidad}</td>
                                                                                                                									<td>Grupo ${g.id}</td>
                                                                                                                								</tr>
                                                                                                                							`).join('')}
					<tr class="total-row">
						<td colspan="4"><strong>TOTAL PIEZAS</strong></td>
						<td><strong>${grupos.reduce((sum, g) => sum + g.cantidad, 0)}</strong></td>
						<td></td>
					</tr>
				</tbody>
			</table>

			<div class="tableros-section">
				<h3 style="margin: 8px 0 4px 0; font-size: 12px;">DISTRIBUCIÓN EN TABLEROS</h3>
		`;

            Array.from({
                length: parseInt(cantT.value) || 1
            }, (_, i) => {
                const piezasEnTablero = tablerosLayout[i] || [];
                const piezasColocadas = piezasEnTablero.filter(p => p.x >= 0);
                const areaUsada = piezasColocadas.reduce((sum, p) => {
                    let w = p.rot ? p.a : p.l;
                    let h = p.rot ? p.l : p.a;
                    return sum + (w * h);
                }, 0);
                const eficiencia = tablero.w * tablero.h > 0 ?
                    ((areaUsada / (tablero.w * tablero.h)) * 100).toFixed(1) : 0;

                let imgData = canvases[i] ? canvases[i].toDataURL("image/png") : "";

                contenidoImpresion += `
		<div class="tablero-item">
			<div class="tablero-title">TABLERO ${i + 1}</div>
			<div class="tablero-stats">${piezasColocadas.length} piezas <br>${eficiencia}% uso</div>
			${imgData ? `<img src="${imgData}" style="max-width:100%;border:1px solid #ccc;">` : ""}
		</div>`;
            });

            contenidoImpresion += `
			</div>

			<div style="margin-top: 10px; padding: 5px; border-top: 1px solid #ccc; font-size: 9px;">
				<strong>Espesor:</strong>
				□ 3mm □ 5mm □ 6mm □ 8mm □ 10mm □ 12mm □ 15mm □ 18mm □ 20mm □ 22mm □ Otro: ______
			</div>

			<div class="no-print" style="text-align: center; margin-top: 15px;">
				<button onclick="window.print()" style="padding: 8px 15px; font-size: 12px;">Imprimir</button>
				<button onclick="window.close()" style="padding: 8px 15px; font-size: 12px; margin-left: 10px;">Cerrar</button>
			</div>
		</body>
		</html>`;

            const iframeDoc = hiddenIframe.contentDocument || hiddenIframe.contentWindow.document;
            iframeDoc.open();
            iframeDoc.write(contenidoImpresion);
            iframeDoc.close();

            hiddenIframe.onload = function() {
                hiddenIframe.contentWindow.focus();
                hiddenIframe.contentWindow.print();
            };
        }

        /* ----------  EVENTOS  ---------- */
        if (btnAdd) {
            btnAdd.addEventListener('click', agregarGrupo);
        }

        [largoT, anchoT, cantT].forEach(el => {
            if (el) {
                el.addEventListener('input', () => {
                    tablero.w = parseFloat(largoT.value) || 0;
                    tablero.h = parseFloat(anchoT.value) || 0;
                    if (tablero.w && tablero.h) refresh();
                });
            }
        });

        // Evento para el botón de actualizar
        if (btnUpd) {
            btnUpd.addEventListener('click', refresh);
        }

        if ($('btn-guardar-cambios')) {
            $('btn-guardar-cambios').addEventListener('click', guardarCambiosGrupo);
        }

        if (btnImprimir) {
            btnImprimir.addEventListener("click", (e) => {
                e.preventDefault();
                imprimirCorte();
            });
        }
        /* ----------  INIT  ---------- */
        function init() {
            if (largoT) largoT.value = 244;
            if (anchoT) anchoT.value = 122;

            tablero.w = parseFloat(largoT?.value) || 244;
            tablero.h = parseFloat(anchoT?.value) || 122;

            if (canvas) {
                canvas.width = canvas.offsetWidth;
                canvas.height = canvas.offsetHeight;
            }

            refresh();
        }

        window.addEventListener('DOMContentLoaded', init);
    </script>
@endpush
