import { Outlet } from "react-router";
import Navbar from "@/components/organisms/Navbar";

export default function GlobalLayout() {
  return (
    <div className="min-h-screen bg-gray-50">
      <Navbar />
      <main className="container mx-auto py-8 px-4">
        <Outlet />
      </main>
    </div>
  );
}
