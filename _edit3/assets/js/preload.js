async function fetchImageList () {
  try {
    const response = await fetch ('assets/php/test.php'); // Ruta de prueba
    if (!response.ok) {
      const errorText = await response.text ();
      throw new Error (
        `Error fetching the image list: ${response.status} ${response.statusText} - ${errorText}`
      );
    }
    const imageList = await response.json ();
    return imageList;
  } catch (error) {
    console.error (error);
    return [];
  }
}
