-- ============================================
-- Base de Datos: Biblioteca UPB
-- Módulo de Inventarios
-- ============================================

CREATE DATABASE IF NOT EXISTS biblioteca_upb
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci;

USE biblioteca_upb;

-- ============================================
-- Tabla: usuarios
-- Almacena estudiantes, externos y admins
-- ============================================
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'estudiante', 'externo') NOT NULL DEFAULT 'estudiante',
    codigo VARCHAR(20) DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- Tabla: categorias
-- Tipos de material bibliográfico
-- ============================================
CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE
);

-- ============================================
-- Tabla: editoriales
-- Casas editoriales de publicaciones
-- ============================================
CREATE TABLE IF NOT EXISTS editoriales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    pais VARCHAR(100) DEFAULT NULL
);

-- ============================================
-- Tabla: autores
-- Autores de materiales bibliográficos
-- ============================================
CREATE TABLE IF NOT EXISTS autores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL
);

-- ============================================
-- Tabla: materiales
-- Libros, papers, tesis, proyectos de grado
-- ============================================
CREATE TABLE IF NOT EXISTS materiales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    categoria_id INT NOT NULL,
    editorial_id INT DEFAULT NULL,
    isbn VARCHAR(20) DEFAULT NULL,
    anio_publicacion INT DEFAULT NULL,
    cantidad_total INT NOT NULL DEFAULT 1,
    cantidad_disponible INT NOT NULL DEFAULT 1,
    descripcion TEXT DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id),
    FOREIGN KEY (editorial_id) REFERENCES editoriales(id)
);

-- ============================================
-- Tabla: material_autor (relación N:M)
-- Un material puede tener varios autores
-- Un autor puede tener varios materiales
-- ============================================
CREATE TABLE IF NOT EXISTS material_autor (
    material_id INT NOT NULL,
    autor_id INT NOT NULL,
    PRIMARY KEY (material_id, autor_id),
    FOREIGN KEY (material_id) REFERENCES materiales(id) ON DELETE CASCADE,
    FOREIGN KEY (autor_id) REFERENCES autores(id) ON DELETE CASCADE
);

-- ============================================
-- Tabla: prestamos
-- Registro de préstamos de materiales
-- ============================================
CREATE TABLE IF NOT EXISTS prestamos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    material_id INT NOT NULL,
    fecha_prestamo DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_devolucion_esperada DATETIME NOT NULL,
    fecha_devolucion_real DATETIME DEFAULT NULL,
    estado ENUM('activo', 'devuelto', 'vencido') NOT NULL DEFAULT 'activo',
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (material_id) REFERENCES materiales(id)
);

-- ============================================
-- Datos iniciales
-- ============================================

-- Categorías
INSERT INTO categorias (nombre) VALUES
('Libro'),
('Paper'),
('Proyecto de Grado'),
('Tesis');

-- Usuario admin por defecto (password: admin123)
INSERT INTO usuarios (nombre, email, password, rol) VALUES
('Bibliotecario Admin', 'admin@upb.edu', '$2y$10$QZke9YyLOBevMgIgv/x6FuIrpXRbOvtYGWFwdsER8iz16Durl8n5O', 'admin');

-- Editoriales de ejemplo
INSERT INTO editoriales (nombre, pais) VALUES
('Pearson', 'Estados Unidos'),
('McGraw-Hill', 'Estados Unidos'),
('Springer', 'Alemania'),
('Editorial UPB', 'Bolivia');

-- Autores de ejemplo
INSERT INTO autores (id, nombre, apellido) VALUES
(1, 'Thomas', 'Cormen'),
(2, 'Abraham', 'Silberschatz'),
(3, 'Andrew', 'Tanenbaum'),
(4, 'Robert', 'Martin'),
(5, 'Donald', 'Knuth'),
(6, 'Grady', 'Booch'),
(7, 'Martin', 'Fowler'),
(8, 'Philip', 'Kotler'),
(9, 'Warren', 'Buffett'),
(10, 'Benjamin', 'Graham'),
(11, 'Paul', 'Samuelson'),
(12, 'Hans', 'Kelsen'),
(13, 'H.L.A.', 'Hart'),
(14, 'Stephen', 'Timoshenko'),
(15, 'Karl', 'Terzaghi'),
(16, 'James', 'Maxwell'),
(17, 'Nikola', 'Tesla'),
(18, 'Richard', 'Feynman'),
(19, 'Claude', 'Shannon'),
(20, 'Alan', 'Turing'),
(21, 'Grace', 'Hopper'),
(22, 'Ada', 'Lovelace'),
(23, 'Manuel', 'Castells'),
(24, 'Jürgen', 'Habermas'),
(25, 'Marshall', 'McLuhan'),
(26, 'Daniel', 'Kahneman'),
(27, 'Adam', 'Smith'),
(28, 'John Maynard', 'Keynes'),
(29, 'Milton', 'Friedman'),
(30, 'Joseph', 'Stiglitz'),
(31, 'Michael', 'Porter'),
(32, 'Peter', 'Drucker'),
(33, 'W. Edwards', 'Deming'),
(34, 'Taiichi', 'Ohno'),
(35, 'Carlos', 'Gómez'),
(36, 'Ana María', 'Flores'),
(37, 'Roberto', 'Arce'),
(38, 'Patricia', 'Vargas'),
(39, 'Mauricio', 'Copa'),
(40, 'Alejandra', 'Salazar');

-- Materiales de ejemplo
INSERT INTO materiales (id, titulo, categoria_id, editorial_id, isbn, anio_publicacion, cantidad_total, cantidad_disponible, descripcion) VALUES
(1, 'Introduction to Algorithms', 1, 1, '978-0262033848', 2009, 5, 5, 'Texto clásico sobre algoritmos y estructuras de datos.'),
(2, 'Fundamentos de Bases de Datos', 1, 2, '978-0078021534', 2019, 3, 3, 'Referencia completa sobre diseño y gestión de bases de datos.'),
(3, 'Redes de Computadoras', 1, 1, '978-0132126953', 2011, 4, 4, 'Texto fundamental sobre redes y protocolos de comunicación.'),
(4, 'Clean Code', 1, 1, '978-0132350884', 2008, 2, 2, 'Guía para escribir código limpio y mantenible.'),
(5, 'Machine Learning en la Educación Superior', 2, NULL, NULL, 2023, 1, 1, 'Paper sobre aplicaciones de ML en universidades.'),
(6, 'Sistema de Gestión Académica UPB', 3, 4, NULL, 2024, 1, 1, 'Proyecto de grado sobre digitalización de procesos académicos.'),
(7, 'Análisis de Redes Sociales en Bolivia', 4, 4, NULL, 2022, 1, 1, 'Tesis doctoral sobre el impacto de redes sociales.'),
(8, 'The Art of Computer Programming', 1, 3, '978-0201896831', 1997, 2, 2, 'Obra fundamental sobre análisis de algoritmos y programación matemática.'),
(9, 'Análisis y Diseño Orientado a Objetos', 1, 1, '978-0201895513', 2007, 3, 3, 'Guía clásica para el diseño de software orientado a objetos.'),
(10, 'Refactoring: Improving the Design of Existing Code', 1, 1, '978-0134757599', 2018, 4, 4, 'Principios y patrones para mejorar la estructura del código existente.'),
(11, 'Dirección de Marketing', 1, 1, '978-6073212458', 2012, 5, 5, 'El texto de referencia global sobre teoría y práctica del marketing.'),
(12, 'El Inversor Inteligente', 1, 2, '978-8449331084', 2003, 6, 6, 'El libro definitivo sobre la inversión en valor y finanzas corporativas.'),
(13, 'Economía', 1, 2, '978-6071502865', 2010, 4, 4, 'Libro de texto clásico sobre microeconomía y macroeconomía moderna.'),
(14, 'Teoría Pura del Derecho', 1, NULL, '978-9505085446', 1960, 5, 5, 'Exposición del positivismo jurídico y la estructura de las normas.'),
(15, 'El Concepto de Derecho', 1, NULL, '978-9505080038', 1961, 3, 3, 'Análisis filosófico de la naturaleza del derecho y el sistema legal.'),
(16, 'Mecánica de Materiales', 1, 2, '978-6075191065', 2013, 3, 3, 'Libro de texto sobre resistencia de materiales y elasticidad.'),
(17, 'Mecánica de Suelos en la Ingeniería Práctica', 1, 3, '978-0471833130', 1996, 2, 2, 'Texto fundamental sobre cimentaciones y comportamiento del suelo.'),
(18, 'Tratado de Electricidad y Magnetismo', 1, NULL, '978-1108015035', 1873, 1, 1, 'Obra clásica que unifica la teoría electromagnética.'),
(19, 'Física de Feynman (Vol. 1)', 1, 1, '978-0201021158', 1963, 3, 3, 'Lecciones clásicas sobre mecánica, radiación y calor.'),
(20, 'La Riqueza de las Naciones', 1, NULL, '978-8420650968', 1776, 2, 2, 'El libro fundacional de la economía clásica y el libre mercado.'),
(21, 'Teoría General del Empleo, el Interés y el Dinero', 1, NULL, '978-9681602444', 1936, 3, 3, 'Obra clave de la macroeconomía y la intervención estatal.'),
(22, 'Capitalismo y Libertad', 1, NULL, '978-8472096752', 1962, 2, 2, 'Defensa de la libertad económica y el liberalismo de mercado.'),
(23, 'El Malestar en la Globalización', 1, NULL, '978-8430604920', 2002, 4, 4, 'Crítica a las políticas de los organismos financieros globales.'),
(24, 'Ventaja Competitiva', 1, 2, '978-6074152541', 1985, 5, 5, 'Cómo crear y mantener un rendimiento superior en los negocios.'),
(25, 'La Práctica de la Administración', 1, 1, '978-8426565150', 1954, 3, 3, 'El origen de la administración por objetivos y el management moderno.'),
(26, 'Calidad, Productividad y Competitividad', 1, 3, '978-8487189227', 1986, 2, 2, 'Los 14 puntos para la transformación de la gestión de la calidad.'),
(27, 'El Sistema de Producción Toyota', 1, NULL, '978-8449306150', 1988, 3, 3, 'Fundamentos de la manufactura esbelta (Lean) y eliminación de desperdicios.'),
(28, 'Pensar Rápido, Pensar Despacio', 1, NULL, '978-8499922072', 2011, 4, 4, 'Análisis de los sesgos cognitivos y la toma de decisiones económicas.'),
(29, 'La Era de la Información (Vol. 1)', 1, NULL, '978-8420642475', 1996, 2, 2, 'Estudio sociológico sobre la sociedad red y la era digital.'),
(30, 'Teoría de la Acción Comunicativa', 1, NULL, '978-8430603398', 1981, 2, 2, 'Análisis de la racionalidad y la comunicación en la sociedad moderna.'),
(31, 'Comprender los Medios de Comunicación', 1, NULL, '978-8449302400', 1964, 3, 3, 'La teoría de los medios como extensiones tecnológicas del ser humano.'),
(32, 'Introducción al Derecho Constitucional Boliviano', 1, 4, '978-9995400230', 2018, 4, 4, 'Análisis del ordenamiento constitucional y los derechos en Bolivia.'),
(33, 'Código Civil Boliviano Comentado', 1, 4, '978-9995402340', 2021, 5, 5, 'Explicación detallada de las normas y contratos civiles bolivianos.'),
(34, 'Diseño Estructural Sismorresistente', 1, 4, '978-9995403560', 2019, 3, 3, 'Guía técnica para el diseño de estructuras de hormigón en Bolivia.'),
(35, 'Finanzas Corporativas Aplicadas', 1, 1, '978-9995404560', 2020, 3, 3, 'Casos prácticos de valoración de empresas y presupuesto de capital.'),
(36, 'Investigación de Operaciones para Ingenieros', 1, 2, '978-9995405560', 2017, 4, 4, 'Modelos de optimización matemática para toma de decisiones industriales.'),
(37, 'Sistemas Distribuidos y Cloud Computing', 1, 3, '978-9995406560', 2022, 3, 3, 'Conceptos de arquitectura de software para la nube e internet de las cosas.'),
(38, 'Una Teoría Matemática de la Comunicación', 2, NULL, NULL, 1948, 1, 1, 'El paper fundacional de la teoría de la información y compresión de datos.'),
(39, 'Sobre Números Computables y su Aplicación', 2, NULL, NULL, 1936, 1, 1, 'El paper clásico que define la máquina de Turing y los límites de la computación.'),
(40, 'Análisis del Potencial de Explotación de Litio en Bolivia', 2, 4, NULL, 2021, 1, 1, 'Paper sobre la viabilidad industrial y económica del litio en el Salar de Uyuni.'),
(41, 'Modelado Macroeconómico de la Inflación en Bolivia (2010-2020)', 2, 4, NULL, 2022, 1, 1, 'Investigación cuantitativa de variables monetarias y estabilidad cambiaria.'),
(42, 'Propuesta de una Red 5G Sostenible para el Eje Troncal de Bolivia', 2, NULL, NULL, 2023, 1, 1, 'Paper sobre diseño de radioenlaces y eficiencia energética para telecomunicaciones.'),
(43, 'Algoritmos Genéticos Aplicados a la Optimización del Tráfico Urbano', 2, NULL, NULL, 2022, 1, 1, 'Estudio sobre rutas dinámicas para descongestionar el tráfico vehicular.'),
(44, 'Impacto de la Inteligencia Artificial en el Empleo en América Latina', 2, NULL, NULL, 2024, 1, 1, 'Análisis de automatización de tareas y reestructuración del mercado laboral.'),
(45, 'Evaluación Geotécnica de Estabilidad de Taludes en la Ciudad de La Paz', 2, 4, NULL, 2020, 1, 1, 'Investigación sobre riesgos de deslizamientos en zonas de pendiente pronunciada.'),
(46, 'Uso de Redes Neuronales Convolucionales para Detección de Plagas agrícolas', 2, NULL, NULL, 2023, 1, 1, 'Paper sobre visión artificial para la optimización de cultivos de quinua.'),
(47, 'El Contrato Social Electrónico: Firma Digital y Derecho en Bolivia', 2, 4, NULL, 2019, 1, 1, 'Estudio legal sobre la validez de contratos electrónicos en el derecho comercial.'),
(48, 'Comportamiento del Consumidor Generación Z ante el E-commerce', 2, NULL, NULL, 2023, 1, 1, 'Estudio sobre patrones de compra y lealtad de marca en medios digitales.'),
(49, 'Diseño de un Sistema de Gestión de Seguridad de la Información en Bancos', 2, NULL, NULL, 2022, 1, 1, 'Propuesta de arquitectura basada en la norma ISO 27001 para banca digital.'),
(50, 'Estudio Comparativo de Hormigones Autocompactantes con Fibras', 2, NULL, NULL, 2021, 1, 1, 'Investigación de propiedades mecánicas y trabajabilidad del hormigón.'),
(51, 'Análisis del Flujo de Caja en Startups Tecnológicas de Bolivia', 2, 4, NULL, 2023, 1, 1, 'Paper sobre estrategias de supervivencia financiera y rondas de inversión.'),
(52, 'Optimización de Procesos Logísticos en la Distribución de Medicamentos', 2, NULL, NULL, 2022, 1, 1, 'Paper sobre modelos de ruteo vehicular con ventanas de tiempo.'),
(53, 'Libertad de Expresión y Regulación de Fake News en Redes Sociales', 2, NULL, NULL, 2021, 1, 1, 'Paper sobre retos del derecho ante la desinformación en plataformas digitales.'),
(54, 'Diseño de Mezclas Asfálticas Modificadas con Polímeros Reciclados', 2, NULL, NULL, 2023, 1, 1, 'Paper sobre pavimentos ecológicos de alta durabilidad en climas de altura.'),
(55, 'Arquitecturas Microservicios en Aplicaciones Fintech en Bolivia', 2, 4, NULL, 2024, 1, 1, 'Estudio sobre escalabilidad, tolerancia a fallos y latencia de transacciones.'),
(56, 'Estrategias de Neuromarketing Aplicadas al Retail en Bolivia', 2, 4, NULL, 2022, 1, 1, 'Paper sobre estímulos sensoriales y comportamiento de compra en supermercados.'),
(57, 'La Estructura Impositiva de Bolivia y su Impacto en el Emprendimiento', 2, NULL, NULL, 2020, 1, 1, 'Análisis crítico sobre régimen tributario simplificado y formalización.'),
(58, 'Algoritmos de Cifrado Homomórfico para Privacidad de Datos Médicos', 2, NULL, NULL, 2023, 1, 1, 'Investigación sobre computación segura en la nube para registros de salud.'),
(59, 'Análisis de la Huella de Carbono en la Construcción de Edificios en Cochabamba', 2, 4, NULL, 2021, 1, 1, 'Evaluación del impacto ambiental de materiales de construcción locales.'),
(60, 'El Arbitraje Comercial como Solución de Conflictos en Contratos del Estado', 2, NULL, NULL, 2022, 1, 1, 'Análisis de la normativa y la jurisprudencia arbitral en Bolivia.'),
(61, 'Uso de UAVs (Drones) en el Catastro Urbano Multifinalitario', 2, NULL, NULL, 2020, 1, 1, 'Metodología fotogramétrica de alta precisión para regularización de tierras.'),
(62, 'El Rol de la Banca de Desarrollo en la Inclusión Financiera Rural', 2, 4, NULL, 2019, 1, 1, 'Estudio del impacto de créditos productivos en el altiplano boliviano.'),

-- --- PROYECTOS DE GRADO (categoria_id = 3) ---
(63, 'Diseño e Implementación de un Sistema ERP para PYMEs en Bolivia', 3, 4, NULL, 2024, 1, 1, 'Proyecto enfocado en la automatización de inventarios, ventas y contabilidad.'),
(64, 'Implementación de Paneles Solares para Iluminación del Campus UPB La Paz', 3, 4, NULL, 2023, 1, 1, 'Estudio de viabilidad técnica, retorno de inversión y reducción de emisiones.'),
(65, 'Diseño Estructural del Puente Vehicular en la Zona Sur de La Paz', 3, 4, NULL, 2022, 1, 1, 'Modelado matemático y estructural de un puente atirantado de hormigón.'),
(66, 'Plan de Negocios para la Exportación de Café Orgánico Yungueño a Europa', 3, 4, NULL, 2024, 1, 1, 'Análisis de mercado, logística internacional y plan estratégico de marketing.'),
(67, 'Desarrollo de una Aplicación de Telemedicina para Zonas Rurales de Oruro', 3, 4, NULL, 2023, 1, 1, 'Plataforma web-móvil para consultas básicas y triaje con conectividad limitada.'),
(68, 'Optimización del Proceso de Envasado en una Planta de Alimentos', 3, 4, NULL, 2021, 1, 1, 'Rediseño de línea de producción basado en Lean Manufacturing y Six Sigma.'),
(69, 'Análisis de Riesgo Crediticio en Créditos de Vivienda Social en Bolivia', 3, 4, NULL, 2022, 1, 1, 'Modelo predictivo de mora bancaria utilizando técnicas estadísticas de regresión.'),
(70, 'Diseño de un Centro de Distribución Automatizado para una Empresa de Retail', 3, 4, NULL, 2023, 1, 1, 'Simulación de operaciones de picking y distribución interna de mercancías.'),
(71, 'Desarrollo de un Framework PHP Basado en Patrón MVC para Aprendizaje', 3, 4, NULL, 2024, 1, 1, 'Herramienta didáctica para la materia de Programación Web en la UPB.'),
(72, 'Estudio de Impacto Ambiental del Tren Metropolitano de Cochabamba', 3, 4, NULL, 2020, 1, 1, 'Evaluación cualitativa y cuantitativa de ruido, emisiones y afectación urbana.'),
(73, 'Diseño de un Sistema de Gestión de Residuos Sólidos para el Municipio de Achocalla', 3, 4, NULL, 2021, 1, 1, 'Propuesta de clasificación en origen, recolección diferenciada y compostaje.'),
(74, 'Plan de Transformación Digital para una Cooperativa de Ahorro y Crédito', 3, 4, NULL, 2023, 1, 1, 'Estrategia de adopción de banca móvil, digitalización de trámites y seguridad.'),
(75, 'Diseño de una Planta Depuradora de Aguas Residuales en industrias Lácteas', 3, 4, NULL, 2022, 1, 1, 'Dimensionamiento de reactores biológicos y tratamientos fisicoquímicos.'),
(76, 'Desarrollo de un Asistente Virtual IA para Consultas Académicas UPB', 3, 4, NULL, 2024, 1, 1, 'Chatbot inteligente que asiste a estudiantes en inscripciones y trámites.'),
(77, 'Diseño Geométrico y de Pavimentos de la Carretera Viacha - Hilata', 3, 4, NULL, 2020, 1, 1, 'Proyecto vial aplicando normas AASHTO para pavimentos flexibles de alto tráfico.'),
(78, 'Plan Estratégico de Marketing para el Lanzamiento de una Fintech en Bolivia', 3, 4, NULL, 2023, 1, 1, 'Estrategia digital enfocada en adquisición de usuarios y posicionamiento de marca.'),
(79, 'Sistema de Control y Monitoreo de Temperatura en Invernaderos Hidropónicos', 3, 4, NULL, 2022, 1, 1, 'Prototipo basado en microcontroladores e Internet de las Cosas (IoT).'),
(80, 'Valoración de la Empresa Nacional de Electricidad (ENDE) bajo Flujo Descontado', 3, 4, NULL, 2021, 1, 1, 'Estudio financiero y proyección de flujos de caja libre de la estatal eléctrica.'),
(81, 'Diseño de un Sistema de Ventilación para una Mina Subterránea en Potosí', 3, 4, NULL, 2023, 1, 1, 'Cálculo de caudal de aire, pérdidas de carga y selección de ventiladores principales.'),
(82, 'Implementación de un Sistema de Gestión de Calidad bajo ISO 9001 en Imprenta', 3, 4, NULL, 2022, 1, 1, 'Estandarización de procesos de pre-prensa, impresión y despacho de productos.'),
(83, 'Análisis de la Responsabilidad Civil de los Proveedores en el Comercio Electrónico', 3, 4, NULL, 2021, 1, 1, 'Proyecto enfocado en la protección al consumidor en la legislación boliviana.'),
(84, 'Desarrollo de una API Rest para Reserva de Espacios Deportivos en La Paz', 3, 4, NULL, 2024, 1, 1, 'Backend escalable en Node.js con pasarela de pagos QR integrada.'),
(85, 'Diseño de una Estructura Metálica para un Tinglado Deportivo Multiuso', 3, 4, NULL, 2020, 1, 1, 'Análisis estructural ante cargas de viento y granizo en el altiplano.'),
(86, 'Plan de Negocios para la Creación de una Empresa de Reciclaje de Neumáticos', 3, 4, NULL, 2022, 1, 1, 'Estudio técnico-económico de trituración mecánica de caucho en Cochabamba.'),
(87, 'Análisis Crítico del Recurso de Casación en la Jurisprudencia Penal Boliviana', 3, 4, NULL, 2023, 1, 1, 'Estudio de fallos del Tribunal Supremo de Justicia y vulneraciones constitucionales.'),

-- --- TESIS (categoria_id = 4) ---
(88, 'Modelado Dinámico de Sistemas Macroeconómicos en Países Latinoamericanos', 4, 4, NULL, 2020, 1, 1, 'Tesis doctoral sobre el comportamiento de políticas fiscales ante shocks externos.'),
(89, 'Optimización Multiobjetivo en Logística de Distribución de Gas Licuado', 4, 4, NULL, 2021, 1, 1, 'Modelos avanzados de optimización heurística para flotas de distribución.'),
(90, 'Estudio de la Resistencia de Columnas de Hormigón Armado Reforzadas con CFRP', 4, 4, NULL, 2019, 1, 1, 'Tesis sobre el uso de polímeros reforzados con fibra de carbono en estructuras.'),
(91, 'Análisis del Régimen Tributario de Hidrocarburos y el Desarrollo Regional', 4, 4, NULL, 2022, 1, 1, 'Investigación jurídica e impositiva de la ley de hidrocarburos en Bolivia.'),
(92, 'Algoritmos de Aprendizaje Profundo en la Detección de Arritmias Cardíacas', 4, 4, NULL, 2023, 1, 1, 'Tesis doctoral que propone modelos convolucionales sobre datos de ECG.'),
(93, 'El Impacto del Crédito de Vivienda de Interés Social en la Pobreza Urbana', 4, 4, NULL, 2021, 1, 1, 'Investigación empírica en el área metropolitana de Cochabamba y El Alto.'),
(94, 'Diseño de Mezclas de Hormigón de Alta Resistencia usando Puzolana Local', 4, 4, NULL, 2020, 1, 1, 'Tesis de maestría sobre cemento puzolánico e incremento de resistencia mecánica.'),
(95, 'La Prueba Ilícita en el Proceso Penal Boliviano: Garantía vs. Impunidad', 4, 4, NULL, 2022, 1, 1, 'Análisis jurisprudencial sobre la exclusión de pruebas obtenidas ilegalmente.'),
(96, 'Estrategias de Precios Dinámicos basadas en Machine Learning para E-commerce', 4, 4, NULL, 2023, 1, 1, 'Tesis doctoral sobre estimación de elasticidad precio de demanda en tiempo real.'),
(97, 'Estudio del Comportamiento Reológico de Asfaltos Modificados con Polímeros', 4, 4, NULL, 2021, 1, 1, 'Análisis de viscosidad y deformación plástica ante variaciones térmicas extremas.'),
(98, 'Modelos de Gobierno Corporativo y su Efecto en el Rendimiento Financiero', 4, 4, NULL, 2018, 1, 1, 'Tesis de maestría en administración sobre empresas cotizadas en bolsa de Bolivia.'),
(99, 'La Protección del Medio Ambiente en el Derecho Penal Internacional', 4, 4, NULL, 2020, 1, 1, 'Investigación del concepto de ecocidio y la jurisdicción de la Corte Penal Internacional.'),
(100, 'Modelado y Simulación del Proceso de Endulzamiento de Gas en Bolivia', 4, 4, NULL, 2022, 1, 1, 'Tesis de maestría en ingeniería química y procesos sobre remoción de CO2.'),
(101, 'Análisis de la Eficiencia Operativa de los Aeropuertos de Bolivia', 4, 4, NULL, 2023, 1, 1, 'Metodología Data Envelopment Analysis (DEA) aplicada a la gestión aeroportuaria.'),
(102, 'Riesgos Financieros y Coberturas con Derivados en el Sector Exportador', 4, 4, NULL, 2021, 1, 1, 'Análisis de contratos futuros y swaps para mitigar la volatilidad cambiaria.'),
(103, 'La Tutela Civil de los Derechos de la Personalidad ante el Uso de Datos', 4, 4, NULL, 2022, 1, 1, 'Estudio sobre la protección civil ante la recolección masiva de datos en internet.'),
(104, 'Estudio de Materiales Compuestos Eco-amigables con Fibras de Coco y Yeso', 4, 4, NULL, 2019, 1, 1, 'Tesis sobre paneles termoacústicos alternativos para la edificación social.'),
(105, 'Estabilización de Suelos Arcillosos Expansivos mediante Cal y Ceniza de Caña', 4, 4, NULL, 2023, 1, 1, 'Estudio de mejoramiento de subrasantes en pavimentos expuestos a humedad.'),
(106, 'Valuación de Opciones Reales en Proyectos de Inversión de Energía Solar', 4, 4, NULL, 2022, 1, 1, 'Tesis de maestría enfocada en la toma de decisiones estratégicas bajo incertidumbre.'),
(107, 'El Principio de Proporcionalidad en la Detención Preventiva en Bolivia', 4, 4, NULL, 2024, 1, 1, 'Investigación de medidas cautelares y derechos constitucionales en el proceso penal.');

-- Relaciones material-autor
INSERT INTO material_autor (material_id, autor_id) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4),
(8, 5),
(9, 6),
(10, 7),
(11, 8),
(12, 10),
(13, 11),
(14, 12),
(15, 13),
(16, 14),
(17, 15),
(18, 16),
(19, 18),
(20, 27),
(21, 28),
(22, 29),
(23, 30),
(24, 31),
(25, 32),
(26, 33),
(27, 34),
(28, 26),
(29, 23),
(30, 24),
(31, 25),
(32, 38),
(33, 38),
(34, 37),
(35, 36),
(36, 36),
(37, 35),
(38, 19),
(39, 20),
(40, 36),
(41, 36),
(42, 35),
(43, 35),
(44, 36),
(45, 37),
(46, 35),
(47, 38),
(48, 40),
(49, 39),
(50, 37),
(51, 36),
(52, 39),
(53, 40),
(54, 37),
(55, 35),
(56, 40),
(57, 36),
(58, 35),
(59, 37),
(60, 38),
(61, 37),
(62, 36),
(63, 35),
(64, 35),
(65, 37),
(66, 36),
(67, 35),
(68, 39),
(69, 36),
(70, 39),
(71, 35),
(72, 37),
(73, 37),
(74, 39),
(75, 37),
(76, 35),
(77, 37),
(78, 40),
(79, 35),
(80, 36),
(81, 39),
(82, 39),
(83, 38),
(84, 35),
(85, 37),
(86, 39),
(87, 38),
(88, 36),
(89, 39),
(90, 37),
(91, 38),
(92, 35),
(93, 36),
(94, 37),
(95, 38),
(96, 36),
(97, 37),
(98, 36),
(99, 38),
(100, 39),
(101, 39),
(102, 36),
(103, 38),
(104, 37),
(105, 37),
(106, 36),
(107, 38);

-- ============================================
-- Consultas de ejemplo (para demostración)
-- ============================================

-- 1) Listar materiales con su categoría, editorial y autores
-- SELECT m.id, m.titulo, c.nombre AS categoria, e.nombre AS editorial,
--        GROUP_CONCAT(CONCAT(a.nombre, ' ', a.apellido) SEPARATOR ', ') AS autores
-- FROM materiales m
-- JOIN categorias c ON m.categoria_id = c.id
-- LEFT JOIN editoriales e ON m.editorial_id = e.id
-- LEFT JOIN material_autor ma ON m.id = ma.material_id
-- LEFT JOIN autores a ON ma.autor_id = a.id
-- GROUP BY m.id;

-- 2) Buscar materiales por título o autor
-- SELECT m.titulo, c.nombre AS categoria
-- FROM materiales m
-- JOIN categorias c ON m.categoria_id = c.id
-- LEFT JOIN material_autor ma ON m.id = ma.material_id
-- LEFT JOIN autores a ON ma.autor_id = a.id
-- WHERE m.titulo LIKE '%algoritmos%' OR a.apellido LIKE '%cormen%'
-- GROUP BY m.id;

-- 3) Préstamos activos de un usuario
-- SELECT p.id, m.titulo, p.fecha_prestamo, p.fecha_devolucion_esperada
-- FROM prestamos p
-- JOIN materiales m ON p.material_id = m.id
-- WHERE p.usuario_id = 1 AND p.estado = 'activo';

-- 4) Préstamos vencidos
-- SELECT p.id, u.nombre AS usuario, m.titulo, p.fecha_devolucion_esperada
-- FROM prestamos p
-- JOIN usuarios u ON p.usuario_id = u.id
-- JOIN materiales m ON p.material_id = m.id
-- WHERE p.estado = 'activo' AND p.fecha_devolucion_esperada < NOW();

-- 5) Cantidad de materiales por categoría
-- SELECT c.nombre AS categoria, COUNT(*) AS total
-- FROM materiales m
-- JOIN categorias c ON m.categoria_id = c.id
-- GROUP BY c.id;
