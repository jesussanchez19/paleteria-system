# Manual de Usuario

## 1. Objetivo

Este manual explica el uso del sistema de paletería para los roles de vendedor, gerente y administrador. Incluye los flujos principales de operación diaria, configuración y consulta de información.

## 2. Acceso al sistema

### Entrada pública

- La ruta principal puede mostrar el catálogo público o redirigir al inicio de sesión, según la configuración de la computadora de trabajo.
- Si el equipo está marcado como computadora de trabajo, la página principal envía al inicio de sesión.
- Si no está marcado, la página principal muestra el catálogo público.

### Inicio de sesión

1. Ingrese con su correo y contraseña.
2. El sistema redirige automáticamente según el rol:
3. Vendedor: Punto de venta.
4. Gerente: Panel principal.
5. Administrador: Configuración crítica.

## 3. Roles del sistema

### Vendedor

- Puede acceder al punto de venta.
- Puede abrir y cerrar caja.
- Puede registrar ventas.
- No puede administrar productos, reportes ni configuraciones críticas.

### Gerente

- Puede vender en el punto de venta.
- Puede administrar productos.
- Puede registrar entradas de inventario.
- Puede consultar reportes y paneles.
- Puede gestionar vendedores.
- Puede usar módulos de IA, clima y auditoría.
- Puede modificar la configuración operativa.

### Administrador

- Puede acceder a la configuración crítica del sistema.
- Puede configurar APIs, políticas y mantenimiento.
- Puede gestionar respaldos.
- Puede marcar o desmarcar la computadora de trabajo.

## 4. Catálogo público

El catálogo público permite mostrar productos disponibles sin iniciar sesión.

### Qué muestra

- Productos activos.
- Productos con existencia disponible, según configuración.
- Información del negocio.
- Mensajes y horarios del negocio.

### Uso general

1. Ingrese al catálogo.
2. Revise los productos agrupados por categoría.
3. Consulte imágenes, nombre, precio y disponibilidad.
4. Si necesita entrar al sistema, use la opción de acceso para empleados.

## 5. Punto de venta

El punto de venta es la pantalla principal para registrar ventas.

### Flujo recomendado de uso

1. Inicie sesión como vendedor o gerente.
2. Verifique si la caja está abierta.
3. Si la caja está cerrada, capture el monto inicial y ábrala.
4. Seleccione productos para agregarlos al carrito.
5. Revise cantidades y total.
6. Confirme la venta.
7. Entregue o consulte el ticket generado.

### Abrir caja

1. Entre al punto de venta.
2. Capture el monto de apertura.
3. Confirme la operación.
4. A partir de ese momento el sistema registra ventas dentro del turno.

### Registrar una venta

1. Seleccione uno o varios productos.
2. Ajuste la cantidad si es necesario.
3. Revise el carrito antes de confirmar.
4. Presione el botón para finalizar la venta.
5. El sistema descuenta existencias automáticamente.
6. Se genera el ticket con sus detalles.

### Cerrar caja

1. Verifique que el turno pueda cerrarse según las horas mínimas configuradas.
2. Presione la opción de cierre de caja.
3. El sistema guarda la hora de cierre.
4. El monto real puede registrarse posteriormente desde el panel de caja del gerente.

### Recomendaciones para caja

- Abra una sola caja por turno de trabajo.
- No cierre el navegador con ventas pendientes en el carrito.
- Revise que los productos tengan stock suficiente antes de confirmar ventas grandes.

## 6. Ticket de venta

Después de cada venta, el sistema genera un ticket consultable.

### Qué puede hacer

- Ver el detalle de la venta.
- Consultar el identificador del ticket.
- Descargar el ticket en PDF cuando el rol lo permita.

## 7. Gestión de productos

Este módulo está dirigido al gerente.

### Crear un producto

1. Ingrese al módulo de productos.
2. Seleccione la opción para crear.
3. Capture nombre, categoría, precio, stock y demás datos.
4. Si desea, agregue una imagen.
5. Guarde los cambios.

### Editar un producto

1. Busque el producto en la lista.
2. Abra la pantalla de edición.
3. Modifique los campos necesarios.
4. Guarde los cambios.

### Eliminar una imagen

1. Abra la edición del producto.
2. Use la opción para eliminar la imagen actual.
3. Verifique que el producto quede sin imagen.
4. Si necesita una nueva, vuelva a cargarla y guarde.

### Desactivar o eliminar productos

- Use la desactivación cuando no quiera mostrar temporalmente un producto.
- Use la eliminación solo cuando esté seguro de que ya no se utilizará.

## 8. Inventario

El inventario se actualiza principalmente por ventas y entradas de mercancía.

### Registrar entrada de mercancía

1. Ingrese al módulo de productos o inventario.
2. Seleccione el producto correspondiente.
3. Capture la cantidad de entrada.
4. Confirme la operación.
5. El stock del producto aumentará automáticamente.

### Buenas prácticas

- Registre entradas en cuanto llegue la mercancía.
- Revise productos con stock bajo al inicio y al cierre del día.
- Evite capturar cantidades incorrectas para no afectar reportes.

## 9. Reportes

Los reportes están disponibles para el gerente.

### Tipos de reportes

- Reporte general.
- Reporte diario.
- Reporte semanal.
- Reporte por vendedores.
- Gráficas.
- Exportación a PDF en algunos módulos.

### Uso básico

1. Ingrese al panel de reportes.
2. Seleccione el período a consultar.
3. Revise indicadores, ventas, tickets y productos más vendidos.
4. Si lo necesita, descargue el reporte en PDF.

### Qué revisar con frecuencia

- Total vendido del día.
- Número de tickets.
- Productos más vendidos.
- Categorías con mayor movimiento.
- Rendimiento por vendedor.

## 10. Panel de caja para gerente

Este módulo permite revisar turnos y diferencias de efectivo.

### Funciones principales

- Consultar cajas abiertas y cerradas.
- Revisar monto esperado.
- Registrar dinero real contado.
- Detectar sobrantes o faltantes.

### Registrar dinero real

1. Ingrese al panel de caja.
2. Busque el turno correspondiente.
3. Capture el monto real contado.
4. Guarde la información.
5. Revise la diferencia calculada por el sistema.

## 11. Gestión de vendedores

Disponible para el gerente.

### Alta de vendedor

1. Ingrese al módulo de vendedores.
2. Capture nombre, correo y contraseña.
3. Guarde el registro.

### Editar vendedor

1. Localice al vendedor en la lista.
2. Abra la opción de edición.
3. Actualice los datos necesarios.
4. Guarde los cambios.

### Activar o desactivar vendedor

- Si un vendedor está desactivado, no podrá usar el sistema.
- Puede reactivarse desde el mismo módulo.

## 12. Asistente de IA

Disponible para el gerente cuando la API correspondiente está configurada.

### Para qué sirve

- Consultar ventas.
- Revisar tendencias.
- Detectar inventario bajo.
- Obtener resúmenes operativos.

### Ejemplos de preguntas útiles

- Cuáles fueron las ventas de hoy.
- Cuál es el producto más vendido.
- Qué productos tienen inventario bajo.
- Qué categoría vende más.
- Cuál fue el mejor día de la semana.

### Recomendación

- Use preguntas claras y enfocadas para obtener mejores respuestas.

## 13. Clima y análisis con IA

Disponible para el gerente cuando las APIs necesarias están configuradas.

### Funciones

- Consultar el clima actual de la ciudad del negocio.
- Revisar análisis entre clima y ventas.
- Obtener recomendaciones operativas según condiciones climáticas.

### Uso sugerido

1. Abra el módulo de clima.
2. Revise temperatura y condición actual.
3. Entre al análisis de clima.
4. Consulte los insights generados.
5. Use esa información para planear producción o compras.

## 14. Configuración operativa

Disponible para el gerente.

### Qué puede configurarse

- Nombre del negocio.
- Teléfono, dirección y ciudad.
- Horarios de operación.
- Umbral de stock bajo.
- Parámetros de ventas.
- Mensajes del catálogo.
- Alertas y notificaciones.

### Recomendaciones

- Revise los horarios si el catálogo público los muestra incorrectamente.
- Mantenga actualizados los datos del negocio.
- Ajuste el umbral de stock bajo según el volumen real de operación.

## 15. Configuración crítica

Disponible solo para el administrador.

### Funciones principales

- Marcar la computadora como equipo de trabajo.
- Configurar claves de servicios externos.
- Activar o desactivar parámetros críticos del sistema.
- Limpiar caché y logs.
- Probar conexiones.
- Crear, descargar y eliminar respaldos.

### Configuración inicial recomendada

1. Definir si el equipo será computadora de trabajo.
2. Capturar las claves necesarias para IA y clima.
3. Verificar tiempos de sesión y políticas del sistema.
4. Probar conexiones.
5. Crear un respaldo inicial.

## 16. Flujo operativo diario sugerido

### Vendedor

1. Iniciar sesión.
2. Abrir caja con el monto inicial.
3. Registrar ventas durante el turno.
4. Confirmar que no queden ventas pendientes.
5. Cerrar caja al final del turno.

### Gerente

1. Revisar ventas del día.
2. Registrar el dinero real de caja.
3. Verificar stock bajo.
4. Consultar reportes o dashboard.
5. Revisar vendedores, clima o asistente IA si aplica.

### Administrador

1. Revisar configuración crítica si hay incidencias.
2. Verificar conexiones de servicios externos.
3. Crear respaldos periódicos.
4. Ejecutar tareas de mantenimiento cuando sea necesario.

## 17. Solución de problemas frecuentes

### No puedo iniciar sesión

- Verifique correo y contraseña.
- Confirme que el usuario esté activo.
- Si el problema persiste, pida apoyo al gerente o administrador.

### No puedo cerrar la caja

- Revise si ya se cumplieron las horas mínimas configuradas.
- Verifique que la caja esté realmente abierta bajo su usuario.

### Un producto no aparece en el catálogo o en POS

- Revise que esté activo.
- Revise que tenga stock disponible, según la configuración.
- Verifique que la categoría y datos estén correctos.

### El módulo de IA o clima no responde

- Verifique que las claves API estén configuradas por el administrador.
- Revise la conexión a internet.

### La imagen de un producto no se muestra

- Revise que la imagen se haya guardado correctamente.
- Intente eliminarla y cargarla de nuevo.
- Si el sistema usa almacenamiento en la nube, verifique con el administrador la configuración del servicio.

## 18. Recomendaciones de uso

- Mantenga actualizados productos, precios y existencias.
- No comparta credenciales entre usuarios.
- Revise reportes diariamente.
- Registre diferencias de caja el mismo día.
- Cree respaldos periódicos si es administrador.

## 19. Contacto interno sugerido

Para incidencias operativas o técnicas, defina internamente:

- Responsable de ventas.
- Responsable de caja.
- Responsable de configuración.
- Responsable de soporte técnico.

Este documento puede ampliarse con capturas de pantalla, políticas internas y procedimientos específicos del negocio.