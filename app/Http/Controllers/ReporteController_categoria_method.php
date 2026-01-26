private function downloadCategoriaPdf(Request $request)
{
// Group assets by category with count and total value
$categorias = \App\Models\Categoria::withCount('activosFijos')
->with(['activosFijos' => function($query) use ($request) {
// Apply filters if needed
if ($request->departamento_id) {
$query->where('departamento_id', $request->departamento_id);
}
if ($request->ubicacion_id) {
$query->where('ubicacion_id', $request->ubicacion_id);
}
if ($request->responsable_id) {
$query->where('responsable_id', $request->responsable_id);
}
if ($request->estado_activo_id) {
$query->where('estado_activo_id', $request->estado_activo_id);
}
if ($request->fecha_inicio && $request->fecha_fin) {
$query->whereBetween('fecha_adquisicion', [$request->fecha_inicio, $request->fecha_fin]);
}
}])
->get()
->map(function($categoria) {
$categoria->activos_count = $categoria->activosFijos->count();
$categoria->valor_total = $categoria->activosFijos->sum('valor_adquisicion');
return $categoria;
})
->filter(function($categoria) {
return $categoria->activos_count > 0; // Only show categories with assets
});

$pdf = Pdf::loadView('reports.categoria', compact('categorias'))
->setPaper('letter', 'portrait');

return $pdf->stream('reporte_categoria_' . date('Y-m-d_H-i') . '.pdf');
}