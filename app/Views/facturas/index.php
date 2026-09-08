<!-- app/Views/facturas/index.php -->
<td class="text-center">
    <!-- Botón Ver Detalles -->
    <button class="btn btn-info btn-sm" title="Ver detalles">
        <i class="fas fa-eye"></i>
    </button>

    <!-- Botón Imprimir PDF -->
    <a href="<?= base_url('facturas/pdf/' . $factura['id_venta']); ?>" 
       target="_blank" 
       class="btn btn-danger btn-sm" 
       title="Imprimir PDF">
        <i class="fas fa-file-pdf"></i>
    </a>
</td>