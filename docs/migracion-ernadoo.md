# Migrar de ernadoo/qte 2.0.0-RC1

Esta versión de Quick Title Edition incorpora una migración automática para la
versión **ernadoo/qte 2.0.0-RC1** adjunta a este proyecto. Se ejecuta al habilitar
`kaileymsnay/qte`, después de sus migraciones iniciales. No necesita ejecutar SQL
manualmente ni instalar otra extensión de conversión.

## Preparación e instalación

1. Haga una copia completa de la base de datos y los archivos del foro. Pruebe
   primero estos pasos en una copia del foro con phpBB 3.3.8 o posterior.
2. Compruebe que la extensión antigua está completamente actualizada a
   2.0.0-RC1. No se admiten actualizaciones interrumpidas ni versiones anteriores.
3. En el ACP, desactive **ernadoo/qte**. **No pulse «Eliminar datos»**: ambas
   extensiones usan las mismas tablas y esa acción eliminaría los atributos.
4. Suba el contenido de esta extensión a `ext/kaileymsnay/qte/`. Mantenga la
   carpeta antigua `ext/ernadoo/qte/`, desactivada, durante la migración y las
   comprobaciones.
5. Habilite **kaileymsnay/qte**. Si ya estaba instalada y activada, desactívela y
   vuelva a habilitarla después de subir los nuevos archivos. Esto ejecuta las
   migraciones pendientes, conservando la integración con Recent Topics NG.
6. Purgue la caché del foro. Compruebe los atributos en el ACP, las asignaciones
   de varios temas, los atributos predeterminados y obligatorios de los foros,
   y los permisos de usuarios, grupos y roles.

Mantenga la extensión antigua desactivada. No habilite ambas a la vez ni use
«Eliminar datos» en la antigua después de migrar: los datos siguen siendo
compartidos. Para volver a la versión antigua, restaure la copia completa de
archivos y base de datos; esta conversión no ofrece una reversión automática.

## Datos conservados y cambios

- Los atributos conservan sus IDs, orden, textos, claves de idioma, colores,
  imágenes, formatos de fecha y descripciones. Los temas conservan el atributo,
  el usuario que lo asignó y la fecha. Los foros conservan su atributo
  predeterminado y la opción de atributo obligatorio.
- El campo de descripción se amplía de 60 a 255 caracteres.
- El permiso antiguo `m_qte_attr_del` pasa a `m_qte_attr_delete`. Cuando solo
  existe el antiguo se conserva su ID, y con él todas sus asignaciones. Si ya
  existen los dos, se copian las asignaciones que faltan al nuevo. En las
  coincidencias se conserva el valor más restrictivo: NUNCA, NO, SÍ, en ese orden.
  No se conceden permisos predeterminados adicionales. Los permisos de uso de
  cada atributo (`f_qte_attr_*`), edición y administración permanecen intactos.
- El módulo del ACP se adapta al nuevo nombre de extensión conservando su ID
  y posición. Si ya existe un módulo nuevo, el antiguo se desactiva para evitar
  dos entradas activas. La migración invalida las cachés afectadas.
- Se incluyen las cuatro claves personalizadas del ZIP antiguo: `QTE_REUP`,
  `QTE_ROTO`, `QTE_LAUT` y `QTE_LVPINT`, con los textos originales, en inglés,
  español y español informal. Otras personalizaciones locales de archivos de
  idioma o estilos deben trasladarse aparte.

Las rutas de imágenes se conservan literalmente. Si apuntan a
`ext/ernadoo/qte/`, mantenga esos archivos disponibles. Antes de retirar la
carpeta antigua, copie sus imágenes personalizadas y actualice sus rutas en el
ACP de QTE. La base de datos no contiene esos archivos y no puede copiarlos.

Si la migración detecta la extensión antigua activa, otra versión o un esquema,
permisos o módulo de administración incompletos, se detiene con un mensaje.
Corrija el requisito indicado y vuelva a habilitar la nueva extensión. No
elimine datos para intentar resolver el error.

## Pruebas automatizadas

```sh
PHPBB_ROOT_PATH=/ruta/al/codigo/phpBB php tests/ernadoo_migration.php
php tests/recenttopicsng.php
```

La primera prueba necesita el código fuente de phpBB 3.3 y PHP con SQLite3.
Utiliza el migrador, el controlador SQLite y las herramientas de esquema reales
de phpBB con bases en memoria; no accede a la base de datos del foro.
Comprueba la cadena de migraciones completa de QTE sobre el esquema antiguo,
la conservación de datos y permisos, la ampliación de la descripción,
coexistencia de permisos, repetición y rechazo de instalaciones incompatibles.
