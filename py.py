import pandas as pd

# ==========================================
# 1. EXTRACT (Extraer los datos)
# ==========================================
print("Leyendo el archivo original...")
try:
    # AJUSTE 1: Cambiamos el separador a punto y coma (;)
    df = pd.read_csv('obras_infobras.csv', encoding='latin-1', sep=';') 
except Exception as e:
    print(f"Error al leer el archivo: {e}")

# ==========================================
# 2. TRANSFORM (Transformar y Limpiar)
# ==========================================
print("Limpiando nombres de columnas y filtrando por región...")

# AJUSTE 2: Limpiamos los títulos de las columnas de cualquier espacio en blanco oculto
df.columns = df.columns.str.strip()

# Imprimimos las columnas detectadas por si necesitamos verificar el nombre exacto
# (Si sigue fallando, esta línea te mostrará cómo se llama la columna realmente)
print("Columnas encontradas:", df.columns.tolist()[:10], "...")

# Estandarizamos el contenido de la columna
df['Departamento'] = df['Departamento'].astype(str).str.strip().str.upper()

# Aplicamos el filtro para Pasco
df_pasco = df[df['Departamento'] == 'PASCO']

cantidad_obras = len(df_pasco)
print(f"¡Éxito! Se encontraron {cantidad_obras} obras pertenecientes a la región Pasco.")

# ==========================================
# 3. LOAD (Cargar / Guardar)
# ==========================================
print("Guardando el nuevo archivo filtrado...")
df_pasco.to_csv('obras_pasco_limpio.csv', index=False, encoding='utf-8', sep=',')

print("Proceso completado. Archivo listo.")