export interface ProjectEnvironment {
  id: string
  key: string
  name: string
  is_production: boolean
  status: string
  sort_order: number
}

export interface ProjectMembership {
  id: string
  user_id: string
  user_name: string
  project_role: string
  status: string
  joined_at: string | null
}

export interface Project {
  id: string
  key: string
  name: string
  repository_url: string | null
  description: string | null
  status: string
  criticality: string
  archived_at: string | null
  environments: ProjectEnvironment[]
  memberships: ProjectMembership[]
}

export interface ProjectsIndexPageProps {
  can_create: boolean
  projects: Project[]
}

export interface ProjectShowPageProps {
  can_manage: boolean
  can_archive: boolean
  can_reveal_secrets: boolean
  project: Project
  user_options: { id: string, name: string, email: string }[]
  secrets: import('./secrets').Secret[]
}
