import { useState, useEffect } from "react";
import { MapContainer, TileLayer, Marker, Popup } from "react-leaflet";
import L from "leaflet";

const customIcon = new L.Icon({
  iconUrl: "https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png",
  iconRetinaUrl:
    "https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png",
  shadowUrl: "https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png",
  iconSize: [25, 41],
  iconAnchor: [12, 41],
  popupAnchor: [1, -34],
  shadowSize: [41, 41],
});

interface Coordinates {
  lat: number;
  lon: number;
}

interface ParkingMapProps {
  address: string;
  className?: string;
}

export default function ParkingMap({
  address,
  className = "",
}: ParkingMapProps) {
  const [coordinates, setCoordinates] = useState<Coordinates | null>(null);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState(false);

  useEffect(() => {
    const geocodeAddress = async () => {
      if (!address) {
        setError(true);
        setIsLoading(false);
        return;
      }

      try {
        const response = await fetch(
          `https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(address)}&format=json&limit=1`,
          {
            headers: {
              "Accept-Language": "fr",
            },
          },
        );
        const data = await response.json();

        if (data && data.length > 0) {
          setCoordinates({
            lat: parseFloat(data[0].lat),
            lon: parseFloat(data[0].lon),
          });
          setError(false);
        } else {
          setError(true);
        }
      } catch {
        setError(true);
      } finally {
        setIsLoading(false);
      }
    };

    geocodeAddress();
  }, [address]);

  if (isLoading) {
    return (
      <div
        className={`bg-gray-200 rounded-3xl flex items-center justify-center ${className}`}
      >
        <div className="flex flex-col items-center gap-3">
          <div className="w-8 h-8 border-3 border-gray-300 border-t-secondary rounded-full animate-spin" />
          <span className="text-gray-500 text-sm">
            Chargement de la carte...
          </span>
        </div>
      </div>
    );
  }

  if (error || !coordinates) {
    return (
      <div
        className={`bg-gray-200 rounded-3xl flex items-center justify-center ${className}`}
      >
        <div className="text-center p-8">
          <span className="text-4xl mb-3 block">📍</span>
          <p className="text-gray-500">Localisation non disponible</p>
          <p className="text-gray-400 text-sm mt-1">{address}</p>
        </div>
      </div>
    );
  }

  return (
    <div className={`rounded-3xl overflow-hidden ${className}`}>
      <MapContainer
        center={[coordinates.lat, coordinates.lon]}
        zoom={16}
        scrollWheelZoom={false}
        style={{ height: "100%", width: "100%", minHeight: "256px" }}
      >
        <TileLayer
          attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
          url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
        />
        <Marker position={[coordinates.lat, coordinates.lon]} icon={customIcon}>
          <Popup>{address}</Popup>
        </Marker>
      </MapContainer>
    </div>
  );
}
