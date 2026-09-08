<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura #<?= $venta['id_venta']; ?></title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; margin: 0; padding: 20px; }
        .banner { background-color: #0f172a; color: #fff; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .banner table { width: 100%; border-collapse: collapse; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .info-box { background: #f8fafc; border: 1px solid #e2e8f0; padding: 10px; border-radius: 5px; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .items-table th { background: #0f172a; color: #fff; padding: 8px; font-size: 11px; text-transform: uppercase; }
        .items-table td { padding: 8px; border-bottom: 1px solid #e2e8f0; }
        .totals-box { width: 220px; margin-left: auto; background: #f8fafc; border: 1px solid #e2e8f0; padding: 10px; border-radius: 5px; }
        .grand-total { border-top: 2px solid #10b981; color: #059669; font-weight: bold; font-size: 14px; }
        .footer { text-align: center; font-size: 10px; color: #64748b; margin-top: 30px; border-top: 1px solid #e2e8f0; padding-top: 10px; }
    </style>
</head>
<body>

    <!-- Encabezado -->
    <div class="banner">
        <table>
            <tr>
                <td>
                    <h2 style="margin:0;">Facturación App</h2>
                    <small style="color: #94a3b8;">Sistema de Gestión de Ventas</small>
                </td>
                <td style="text-align: right;">
                    <div style="color: #38bdf8; font-weight: bold;">COMPROBANTE DIGITAL</div>
                    <div style="font-size: 16px; font-weight: bold;">FACTURA #<?= str_pad($venta['id_venta'], 6, '0', STR_PAD_LEFT); ?></div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Datos Cliente y Venta -->
    <table class="info-table">
        <tr>
            <td style="width: 49%; vertical-align: top;">
                <div class="info-box">
                    <strong>INFORMACIÓN DEL CLIENTE</strong><hr style="border: 0; border-top: 1px solid #ccc;">
                    <b>Cliente:</b> <?= esc($venta['cliente_nombre']); ?><br>
                    <b>CI/RUC:</b> <?= esc($venta['cliente_identificacion']); ?>
                </div>
            </td>
            <td style="width: 2%;"></td>
            <td style="width: 49%; vertical-align: top;">
                <div class="info-box">
                    <strong>DETALLES DE LA VENTA</strong><hr style="border: 0; border-top: 1px solid #ccc;">
                    <b>Fecha:</b> <?= date('Y-m-d H:i:s', strtotime($venta['created_at'] ?? date('Y-m-d H:i:s'))); ?><br>
                    <b>Vendedor:</b> <?= esc($venta['usuario_nombre']); ?>
                </div>
            </td>
        </tr>
    </table>

    <!-- Tabla de Productos -->
    <table class="items-table">
        <thead>
            <tr>
                <th>Código</th>
                <th>Producto</th>
                <th style="text-align: center;">Cant.</th>
                <th style="text-align: right;">P. Unit.</th>
                <th style="text-align: right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($detalles)): ?>
                <?php foreach ($detalles as $item): ?>
                    <tr>
                        <td><code><?= esc($item['codigo_barras'] ?? $item['codigo'] ?? 'N/A'); ?></code></td>
                        <td><b><?= esc($item['nombre_producto'] ?? $item['producto'] ?? 'Producto'); ?></b></td>
                        <td style="text-align: center;"><?= $item['cantidad']; ?></td>
                        <td style="text-align: right;">$<?= number_format($item['precio_unitario'], 2); ?></td>
                        <td style="text-align: right;"><b>$<?= number_format($item['subtotal'], 2); ?></b></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5" style="text-align: center; color: #888;">Sin detalles registrados</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Totales -->
    <div class="totals-box">
        <table style="width: 100%;">
            <tr>
                <td>Subtotal:</td>
                <td style="text-align: right;">$<?= number_format($venta['total'], 2); ?></td>
            </tr>
            <tr>
                <td>IVA (0%):</td>
                <td style="text-align: right;">$0.00</td>
            </tr>
            <tr class="grand-total">
                <td>Total:</td>
                <td style="text-align: right;">$<?= number_format($venta['total'], 2); ?></td>
            </tr>
        </table>
    </div>

    <!-- Pie -->
    <div class="footer">
        <b>¡Gracias por su compra!</b><br>
        Este documento es un comprobante de venta emitido electrónicamente.
    </div>

</body>
</html>