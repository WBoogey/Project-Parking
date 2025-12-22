import type { ReactNode } from "react";

interface EmptyStateProps {
  icon?: string;
  title: string;
  description?: string;
  action?: ReactNode;
}

export default function EmptyState({
  icon = "📭",
  title,
  description,
  action,
}: EmptyStateProps) {
  return (
    <div className="text-center py-12 animate-fade-in-up">
      <span className="text-6xl mb-4 block">{icon}</span>
      <h3 className="text-xl font-bold text-secondary mb-2">{title}</h3>
      {description && <p className="text-tertiary mb-6">{description}</p>}
      {action}
    </div>
  );
}
