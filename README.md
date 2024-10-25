
# 🎮 GameZone - Web Arcade 🕹️

![GameZone Logo](assets/img/nombre.png)

**GameZone** es un proyecto diseñado para desarrollar y mejorar habilidades en **desarrollo web** utilizando tecnologías clave como **HTML**, **CSS**, **JavaScript**, **PHP** y **SQL**, todo integrado mediante **XAMPP** como servidor local. El proyecto cuenta con una estética inspirada en el mundo de los videojuegos arcade 🎮, con un diseño retro moderno usando los colores vibrantes de la cultura gaming.

---

## 🚀 Tecnologías Utilizadas

Este proyecto combina diversas tecnologías para crear una aplicación web completa:

- **HTML5**: Para la estructura y el contenido del sitio.
- **CSS3 y SASS**: Para los estilos y la personalización visual del sitio, con colores morado y rosa (#cb6ce6 y #ff66c4) inspirados en la estética arcade.
- **JavaScript**: Para la interactividad en el lado del cliente.
- **PHP**: Para la lógica de backend y el manejo dinámico del contenido.
- **MySQL**: Para gestionar la base de datos (integrada con PHP).
- **XAMPP**: Como servidor local para ejecutar PHP y gestionar MySQL.

---

## 🎯 Objetivo del Proyecto

**GameZone** fue creado con el objetivo de:

1. **Desarrollar habilidades prácticas en desarrollo web** combinando frontend y backend.
2. **Trabajar con bases de datos** mediante MySQL para almacenar información como los detalles de los juegos y usuarios.
3. **Familiarizarse con el uso de XAMPP** como entorno de desarrollo local para ejecutar aplicaciones web que utilizan PHP y SQL.
4. Crear una **experiencia visual y temática arcade** para los usuarios, incorporando elementos de diseño interactivos y llamativos.

---

## 🕹️ Funcionalidades Principales

- 🎨 **Galería de Juegos**: Presenta una colección de juegos con imágenes y descripciones.
- ⚙️ **Interactividad en tiempo real** usando JavaScript para mejorar la experiencia del usuario.
- 💾 **Gestión de base de datos** con MySQL para almacenar y recuperar información de juegos.
- 🌐 **Diseño responsivo** adaptado a diferentes dispositivos.
- 🛠️ **PHP Backend** para gestionar la lógica del servidor y las conexiones a la base de datos.

---

## 🎮 Estructura del Proyecto

```
/project-root
├── /public
│   ├── /assets
│   │   ├── /css       # Archivos CSS compilados desde SASS
│   │   ├── /img       # Imágenes, incluyendo el logo y las imágenes de los juegos
│   │   ├── /js        # Scripts JavaScript
│   ├── /sass          # Archivos SASS para los estilos
│   └── index.php      # Página principal con la galería de juegos
├── /config            # Configuración del proyecto (por ejemplo, base de datos)
├── /src
│   ├── /Controllers   # Controladores PHP
│   ├── /Models        # Modelos PHP
│   └── /Views         # Vistas PHP
├── /db                # Scripts SQL y backups de la base de datos
└── README.md          # Descripción del proyecto
```

---

## 🎨 Estilo Visual

El estilo del sitio está inspirado en los **videojuegos arcade** clásicos, con un enfoque en colores vibrantes y una experiencia inmersiva. El esquema de colores se centra en:

- **#cb6ce6** (morado) para destacar elementos interactivos.
- **#ff66c4** (rosa) para resaltar secciones clave.
- **#000000** (negro) como fondo para lograr contraste.

---

## ⚙️ Instalación

1. Clona este repositorio en tu máquina local:

   ```bash
   git clone https://github.com/tu-usuario/nombre-del-repositorio.git
   ```

2. Asegúrate de tener **XAMPP** instalado. Coloca el proyecto en el directorio `htdocs` de XAMPP.

3. Inicia XAMPP y asegúrate de que los módulos **Apache** y **MySQL** estén corriendo.

4. Importa la base de datos MySQL desde el archivo SQL proporcionado:

   ```sql
   CREATE DATABASE gamezone;
   USE gamezone;
   SOURCE /ruta/al/archivo/gamezone.sql;
   ```

5. Accede a la página web desde tu navegador:

   ```bash
   http://localhost/gamezone/public/index.php
   ```

---

## 📈 Próximas Mejoras

- Añadir más **juegos** a la galería con detalles y reseñas.
- Implementar un sistema de **usuarios** para gestionar cuentas y puntuaciones.
- Crear un **sistema de comentarios** para que los usuarios puedan dejar reseñas sobre los juegos.

---

## 🤝 Contribuciones

Si deseas contribuir a este proyecto, no dudes en hacer un fork o crear un pull request. También puedes contactarme en GitHub para discutir mejoras o sugerencias.

---

## 📜 Licencia

Este proyecto está bajo la licencia MIT. Puedes ver más detalles en el archivo [LICENSE](LICENSE).
