import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import {
  isRouteErrorResponse,
  Links,
  Meta,
  Outlet,
  Scripts,
  ScrollRestoration,
} from "react-router";
import { Toaster } from "sonner";
import * as React from "react";

import type { Route } from "./+types/root";
import "./app.css";

const queryClient = new QueryClient();

export const links: Route.LinksFunction = () => [
  { rel: "icon", href: "/favicon.svg", type: "image/svg+xml" },
  { rel: "preconnect", href: "https://fonts.googleapis.com" },
  {
    rel: "preconnect",
    href: "https://fonts.gstatic.com",
    crossOrigin: "anonymous",
  },
  {
    rel: "stylesheet",
    href: "https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap",
  },
];

export function Layout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="fr">
      <head>
        <meta charSet="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta
          name="description"
          content="Trouvez et réservez votre place de parking en quelques clics"
        />
        <meta name="theme-color" content="#1b3d61" />
        <title>ParkShare | Votre place de parking en 2 clics</title>
        <Meta />
        <Links />
      </head>
      <body>
        <QueryClientProvider client={queryClient}>
          {children}
          <Toaster position="top-right" richColors />
        </QueryClientProvider>
        <ScrollRestoration />
        <Scripts />
      </body>
    </html>
  );
}

export default function App() {
  return <Outlet />;
}

export function ErrorBoundary({ error }: Route.ErrorBoundaryProps) {
  const is404 = isRouteErrorResponse(error) && error.status === 404;

  if (is404) {
    return (
      <main className="min-h-screen flex flex-col items-center justify-center bg-primary">
        <span className="text-8xl mb-6 animate-fade-in-up">🅿️</span>
        <h1 className="text-6xl font-bold text-secondary mb-4 animate-fade-in-up animation-delay-100">
          404
        </h1>
        <p className="text-xl text-tertiary mb-8 animate-fade-in-up animation-delay-200">
          Cette page n&apos;existe pas
        </p>
        <a
          href="/"
          className="px-6 py-3 bg-secondary text-white rounded-xl hover:opacity-90 transition-opacity animate-fade-in-up animation-delay-300"
        >
          Retour à l&apos;accueil
        </a>
      </main>
    );
  }

  let details = "Une erreur inattendue s'est produite.";
  let stack: string | undefined;

  if (isRouteErrorResponse(error)) {
    details = error.statusText || details;
  } else if (import.meta.env.DEV && error && error instanceof Error) {
    details = error.message;
    stack = error.stack;
  }

  return (
    <main className="min-h-screen flex flex-col items-center justify-center bg-primary p-4">
      <span className="text-8xl mb-6">⚠️</span>
      <h1 className="text-4xl font-bold text-secondary mb-4">Erreur</h1>
      <p className="text-tertiary mb-8 text-center max-w-md">{details}</p>
      {stack && (
        <pre className="w-full max-w-2xl p-4 overflow-x-auto bg-gray-100 rounded-xl text-sm">
          <code>{stack}</code>
        </pre>
      )}
      <a
        href="/"
        className="mt-8 px-6 py-3 bg-secondary text-white rounded-xl hover:opacity-90 transition-opacity"
      >
        Retour à l&apos;accueil
      </a>
    </main>
  );
}
