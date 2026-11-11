CREATE DATABASE IF NOT EXISTS gruas_autocarga
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE gruas_autocarga;

CREATE TABLE usuarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    usuario VARCHAR(60) NOT NULL UNIQUE,
    email VARCHAR(160) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    rol ENUM('empleado','administrador') NOT NULL,
    estado ENUM('activo','inactivo') NOT NULL DEFAULT 'activo',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE clientes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    telefono VARCHAR(30) NOT NULL,
    email VARCHAR(160) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE gruas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    tipo ENUM('motos','carros') NOT NULL,
    capacidad_peso_kg DECIMAL(10,2) NULL,
    capacidad_carros TINYINT UNSIGNED NOT NULL DEFAULT 0,
    capacidad_motos TINYINT UNSIGNED NOT NULL DEFAULT 0,
    estado ENUM('disponible','ocupada','mantenimiento','inactiva') NOT NULL DEFAULT 'disponible',
    descripcion TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE tarifas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tipo_vehiculo ENUM('moto','carro','camioneta') NOT NULL,
    tarifa_base DECIMAL(12,2) NOT NULL DEFAULT 0,
    km_incluidos DECIMAL(10,2) NOT NULL DEFAULT 0,
    valor_km_adicional DECIMAL(12,2) NOT NULL DEFAULT 0,
    activa TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE servicios (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cliente_id BIGINT UNSIGNED NULL,
    empleado_id INT UNSIGNED NULL,
    grua_id INT UNSIGNED NULL,
    tipo_vehiculo ENUM('moto','carro','camioneta') NOT NULL,
    modelo_vehiculo VARCHAR(120) NULL,
    origen VARCHAR(255) NOT NULL,
    destino VARCHAR(255) NOT NULL,
    fecha_programada DATETIME NULL,
    fecha_realizado DATETIME NULL,
    distancia_km DECIMAL(10,2) NULL,
    valor_estimado DECIMAL(12,2) NULL,
    valor_final DECIMAL(12,2) NULL,
    metodo_pago ENUM('efectivo','transferencia','otro') NULL,
    estado ENUM('pendiente','programado','realizado','cancelado') NOT NULL DEFAULT 'pendiente',
    observaciones TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_servicio_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE SET NULL,
    CONSTRAINT fk_servicio_empleado FOREIGN KEY (empleado_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    CONSTRAINT fk_servicio_grua FOREIGN KEY (grua_id) REFERENCES gruas(id) ON DELETE SET NULL
);

CREATE TABLE gastos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    empleado_id INT UNSIGNED NULL,
    servicio_id BIGINT UNSIGNED NULL,
    tipo ENUM('gasolina','peaje','reparacion','otro') NOT NULL,
    valor DECIMAL(12,2) NOT NULL,
    fecha DATETIME NOT NULL,
    descripcion VARCHAR(255) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_gasto_empleado FOREIGN KEY (empleado_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    CONSTRAINT fk_gasto_servicio FOREIGN KEY (servicio_id) REFERENCES servicios(id) ON DELETE SET NULL
);

CREATE TABLE solicitudes_cotizacion (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cliente_id BIGINT UNSIGNED NULL,
    tipo_vehiculo ENUM('moto','carro','camioneta') NOT NULL,
    modelo_vehiculo VARCHAR(120) NULL,
    origen VARCHAR(255) NOT NULL,
    destino VARCHAR(255) NOT NULL,
    distancia_km DECIMAL(10,2) NULL,
    valor_estimado DECIMAL(12,2) NULL,
    estado ENUM('nueva','contactada','confirmada','descartada') NOT NULL DEFAULT 'nueva',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_cotizacion_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE SET NULL
);

CREATE TABLE actividad (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT UNSIGNED NULL,
    accion VARCHAR(120) NOT NULL,
    entidad VARCHAR(80) NULL,
    entidad_id BIGINT UNSIGNED NULL,
    ip VARCHAR(45) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_actividad_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
);
